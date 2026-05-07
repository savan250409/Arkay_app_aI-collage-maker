@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card border-0" style="box-shadow: none;">
                <div class="card-body p-0">
                    
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-2">
                        <h3 class="font-weight-bold mb-0" style="font-size: 1.5rem;">Category Indexing</h3>
                        <a href="{{ route('sticker-categories.index') }}" class="text-muted" style="font-size: 1.5rem; text-decoration: none;">&times;</a>
                    </div>

                    <div class="alert alert-info mb-4" role="alert" style="background-color: #e1f5fe; border-color: #b3e5fc; color: #0277bd;">
                        <i class="mdi mdi-information-outline mr-2"></i> Drag and drop categories to reorder them. The new order will be saved automatically.
                    </div>
                   
                    <div class="row">
                        <div class="col-md-12">
                            <ul id="sortable" class="list-group">
                                @foreach($categories as $index => $category)
                                    <li class="list-group-item d-flex align-items-center justify-content-between mb-2 border-bottom"
                                        data-id="{{ $category->id }}"
                                        style="background: #fff; border: 1px solid #eee; padding: 0.75rem 1rem; border-radius: 4px;">
                                        
                                        <div class="d-flex align-items-center text-dark">
                                            <div class="drag-handle mr-3 text-muted" style="cursor: grab;">
                                                <i class="mdi mdi-drag-vertical" style="font-size: 1.2rem;"></i>
                                            </div>

                                            <div class="font-weight-bold mr-2" style="font-size: 1rem;">
                                                {{ $index + 1 }}.
                                            </div>

                                            <div class="font-weight-500" style="font-size: 1rem;">
                                                {{ $category->name }}
                                            </div>
                                        </div>

                                        <div class="badge badge-secondary" style="background-color: #6c757d; color: white; font-size: 0.8rem; border-radius: 10px; padding: 0.25rem 0.6rem;">
                                            ID: {{ $category->id }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .list-group-item {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .list-group-item:hover {
            background-color: #fcfcfc !important;
        }
        .drag-handle {
            cursor: grab;
        }
        .drag-handle:active {
            cursor: grabbing !important;
        }
        .ui-sortable-helper {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
            background-color: #fff !important;
            opacity: 0.95;
            border: 1px solid #ddd !important;
        }
        .ui-sortable-placeholder {
            visibility: visible !important;
            background: #f8f9fa !important;
            border: 1px dashed #ccc !important;
            height: 50px !important;
            border-radius: 4px;
            margin-bottom: 0.5rem;
        }
    </style>
    <script>
        $(document).ready(function () {
            $("#sortable").sortable({
                handle: ".drag-handle",
                placeholder: "ui-sortable-placeholder",
                axis: "y",
                update: function (event, ui) {
                    var order = [];
                    $('#sortable li').each(function (index) {
                        order.push({
                            id: $(this).data('id'),
                            row_order: index
                        });
                        // Update the visible number
                        $(this).find('.font-weight-bold.mr-2').text((index + 1) + '.');
                    });

                    $.ajax({
                        url: "{{ route('sticker-categories.update-order') }}",
                        type: 'POST',
                        data: {
                            order: order,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });

                                Toast.fire({
                                    icon: 'success',
                                    title: 'Order updated successfully'
                                });
                            }
                        }
                    });
                }
            });
            $("#sortable").disableSelection();
        });
    </script>
@endsection