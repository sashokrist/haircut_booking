/**
 * Admin JavaScript
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize date pickers
        if ($.fn.datepicker) {
            $('.datepicker').datepicker({
                dateFormat: 'yy-mm-dd'
            });
        }

        // BOOKINGS
        if ($('#booking-list').length) {
            // Load bookings on page load
            loadBookings();

            // Filter bookings
            $('#booking-filter-form').on('submit', function(e) {
                e.preventDefault();
                loadBookings();
            });

            // Reset filters
            $('#booking-filter-reset').on('click', function() {
                $('#booking-filter-form')[0].reset();
                loadBookings();
            });

            // Add booking button
            $('#add-booking-btn').on('click', function() {
                $('#add-booking-modal').show();
            });

            // Close modal
            $('.modal-close').on('click', function() {
                $(this).closest('.booking-modal').hide();
            });

            // Add booking form submission
            $('#add-booking-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: booking_admin_ajax.ajax_url,
                    type: 'POST',
                    data: $(this).serialize() + '&action=add_booking&nonce=' + booking_admin_ajax.nonce,
                    success: function(response) {
                        if (response.success) {
                            $('#add-booking-modal').hide();
                            $('#add-booking-form')[0].reset();
                            showMessage('success', response.data.message);
                            loadBookings();
                        } else {
                            showMessage('error', response.data);
                        }
                    },
                    error: function() {
                        showMessage('error', 'An error occurred. Please try again.');
                    }
                });
            });

            // Edit booking
            $(document).on('click', '.edit-booking', function() {
                var bookingId = $(this).data('id');
                var row = $(this).closest('tr');

                // Fill the edit form with booking data
                $('#edit-booking-id').val(bookingId);
                $('#edit-customer-id').val(row.data('customer-id'));
                $('#edit-customer-name').val(row.find('td:eq(0)').text());
                $('#edit-customer-email').val(row.data('customer-email'));
                $('#edit-customer-phone').val(row.data('customer-phone'));
                $('#edit-service-id').val(row.data('service-id'));
                $('#edit-employee-id').val(row.data('employee-id'));
                $('#edit-appointment-date').val(row.find('td:eq(2)').text());
                $('#edit-appointment-time').val(row.find('td:eq(3)').text());
                $('#edit-status').val(row.data('status'));
                $('#edit-notes').val(row.data('notes'));

                $('#edit-booking-modal').show();
            });

            // Update booking form submission
            $('#edit-booking-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: booking_admin_ajax.ajax_url,
                    type: 'POST',
                    data: $(this).serialize() + '&action=update_booking&nonce=' + booking_admin_ajax.nonce,
                    success: function(response) {
                        if (response.success) {
                            $('#edit-booking-modal').hide();
                            showMessage('success', response.data.message);
                            loadBookings();
                        } else {
                            showMessage('error', response.data);
                        }
                    },
                    error: function() {
                        showMessage('error', 'An error occurred. Please try again.');
                    }
                });
            });

            // Delete booking
            $(document).on('click', '.delete-booking', function() {
                if (confirm('Are you sure you want to delete this booking?')) {
                    var bookingId = $(this).data('id');

                    $.ajax({
                        url: booking_admin_ajax.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'delete_booking',
                            booking_id: bookingId,
                            nonce: booking_admin_ajax.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                showMessage('success', response.data.message);
                                loadBookings();
                            } else {
                                showMessage('error', response.data);
                            }
                        },
                        error: function() {
                            showMessage('error', 'An error occurred. Please try again.');
                        }
                    });
                }
            });
        }

        // SERVICES
        if ($('#service-list').length) {
            // Load services on page load
            loadServices();

            // Add service button
            $('#add-service-btn').on('click', function() {
                $('#add-service-modal').show();
            });

            // Add service form submission
            $('#add-service-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: booking_admin_ajax.ajax_url,
                    type: 'POST',
                    data: $(this).serialize() + '&action=add_service&nonce=' + booking_admin_ajax.nonce,
                    success: function(response) {
                        if (response.success) {
                            $('#add-service-modal').hide();
                            $('#add-service-form')[0].reset();
                            showMessage('success', response.data.message);
                            loadServices();
                        } else {
                            showMessage('error', response.data);
                        }
                    },
                    error: function() {
                        showMessage('error', 'An error occurred. Please try again.');
                    }
                });
            });

            // Edit service
            $(document).on('click', '.edit-service', function() {
                var serviceId = $(this).data('id');
                var row = $(this).closest('tr');

                // Fill the edit form with service data
                $('#edit-service-id').val(serviceId);
                $('#edit-service-name').val(row.find('td:eq(0)').text());
                $('#edit-service-description').val(row.data('description'));
                $('#edit-service-duration').val(row.find('td:eq(1)').text().replace(' min', ''));
                $('#edit-service-price').val(row.find('td:eq(2)').text().replace('$', ''));

                $('#edit-service-modal').show();
            });

            // Update service form submission
            $('#edit-service-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: booking_admin_ajax.ajax_url,
                    type: 'POST',
                    data: $(this).serialize() + '&action=update_service&nonce=' + booking_admin_ajax.nonce,
                    success: function(response) {
                        if (response.success) {
                            $('#edit-service-modal').hide();
                            showMessage('success', response.data.message);
                            loadServices();
                        } else {
                            showMessage('error', response.data);
                        }
                    },
                    error: function() {
                        showMessage('error', 'An error occurred. Please try again.');
                    }
                });
            });

            // Delete service
            $(document).on('click', '.delete-service', function() {
                if (confirm('Are you sure you want to delete this service?')) {
                    var serviceId = $(this).data('id');

                    $.ajax({
                        url: booking_admin_ajax.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'delete_service',
                            service_id: serviceId,
                            nonce: booking_admin_ajax.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                showMessage('success', response.data.message);
                                loadServices();
                            } else {
                                showMessage('error', response.data);
                            }
                        },
                        error: function() {
                            showMessage('error', 'An error occurred. Please try again.');
                        }
                    });
                }
            });
        }

        // EMPLOYEES
        if ($('#employee-list').length) {
            // Load employees on page load
            loadEmployees();

            // Add employee button
            $('#add-employee-btn').on('click', function() {
                $('#add-employee-modal').show();
            });

            // Add employee form submission
            $('#add-employee-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: booking_admin_ajax.ajax_url,
                    type: 'POST',
                    data: $(this).serialize() + '&action=add_employee&nonce=' + booking_admin_ajax.nonce,
                    success: function(response) {
                        if (response.success) {
                            $('#add-employee-modal').hide();
                            $('#add-employee-form')[0].reset();
                            showMessage('success', response.data.message);
                            loadEmployees();
                        } else {
                            showMessage('error', response.data);
                        }
                    },
                    error: function() {
                        showMessage('error', 'An error occurred. Please try again.');
                    }
                });
            });

            // Edit employee
            $(document).on('click', '.edit-employee', function() {
                var employeeId = $(this).data('id');
                var row = $(this).closest('tr');

                // Fill the edit form with employee data
                $('#edit-employee-id').val(employeeId);
                $('#edit-employee-name').val(row.find('td:eq(0)').text());
                $('#edit-employee-email').val(row.find('td:eq(1)').text());
                $('#edit-employee-phone').val(row.find('td:eq(2)').text());

                $('#edit-employee-modal').show();
            });

            // Update employee form submission
            $('#edit-employee-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: booking_admin_ajax.ajax_url,
                    type: 'POST',
                    data: $(this).serialize() + '&action=update_employee&nonce=' + booking_admin_ajax.nonce,
                    success: function(response) {
                        if (response.success) {
                            $('#edit-employee-modal').hide();
                            showMessage('success', response.data.message);
                            loadEmployees();
                        } else {
                            showMessage('error', response.data);
                        }
                    },
                    error: function() {
                        showMessage('error', 'An error occurred. Please try again.');
                    }
                });
            });

            // Delete employee
            $(document).on('click', '.delete-employee', function() {
                if (confirm('Are you sure you want to delete this employee?')) {
                    var employeeId = $(this).data('id');

                    $.ajax({
                        url: booking_admin_ajax.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'delete_employee',
                            employee_id: employeeId,
                            nonce: booking_admin_ajax.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                showMessage('success', response.data.message);
                                loadEmployees();
                            } else {
                                showMessage('error', response.data);
                            }
                        },
                        error: function() {
                            showMessage('error', 'An error occurred. Please try again.');
                        }
                    });
                }
            });
        }

        // CUSTOMERS
        if ($('#customer-list').length) {
            // Load customers on page load
            loadCustomers();

            // Add customer button
            $('#add-customer-btn').on('click', function() {
                $('#add-customer-modal').show();
            });

            // Add customer form submission
            $('#add-customer-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: booking_admin_ajax.ajax_url,
                    type: 'POST',
                    data: $(this).serialize() + '&action=add_customer&nonce=' + booking_admin_ajax.nonce,
                    success: function(response) {
                        if (response.success) {
                            $('#add-customer-modal').hide();
                            $('#add-customer-form')[0].reset();
                            showMessage('success', response.data.message);
                            loadCustomers();
                        } else {
                            showMessage('error', response.data);
                        }
                    },
                    error: function() {
                        showMessage('error', 'An error occurred. Please try again.');
                    }
                });
            });

            // Edit customer
            $(document).on('click', '.edit-customer', function() {
                var customerId = $(this).data('id');
                var row = $(this).closest('tr');

                // Fill the edit form with customer data
                $('#edit-customer-id').val(customerId);
                $('#edit-customer-name').val(row.find('td:eq(0)').text());
                $('#edit-customer-email').val(row.find('td:eq(1)').text());
                $('#edit-customer-phone').val(row.find('td:eq(2)').text());

                $('#edit-customer-modal').show();
            });

            // Update customer form submission
            $('#edit-customer-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: booking_admin_ajax.ajax_url,
                    type: 'POST',
                    data: $(this).serialize() + '&action=update_customer&nonce=' + booking_admin_ajax.nonce,
                    success: function(response) {
                        if (response.success) {
                            $('#edit-customer-modal').hide();
                            showMessage('success', response.data.message);
                            loadCustomers();
                        } else {
                            showMessage('error', response.data);
                        }
                    },
                    error: function() {
                        showMessage('error', 'An error occurred. Please try again.');
                    }
                });
            });

            // Delete customer
            $(document).on('click', '.delete-customer', function() {
                if (confirm('Are you sure you want to delete this customer?')) {
                    var customerId = $(this).data('id');

                    $.ajax({
                        url: booking_admin_ajax.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'delete_customer',
                            customer_id: customerId,
                            nonce: booking_admin_ajax.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                showMessage('success', response.data.message);
                                loadCustomers();
                            } else {
                                showMessage('error', response.data);
                            }
                        },
                        error: function() {
                            showMessage('error', 'An error occurred. Please try again.');
                        }
                    });
                }
            });
        }

        // Helper functions
        function loadBookings() {
            var filters = $('#booking-filter-form').serialize();

            $.ajax({
                url: booking_admin_ajax.ajax_url,
                type: 'POST',
                data: filters + '&action=get_bookings&nonce=' + booking_admin_ajax.nonce,
                beforeSend: function() {
                    $('#booking-list tbody').html('<tr><td colspan="7">Loading...</td></tr>');
                },
                success: function(response) {
                    if (response.success) {
                        var bookings = response.data;
                        var html = '';

                        if (bookings.length === 0) {
                            html = '<tr><td colspan="7">No bookings found.</td></tr>';
                        } else {
                            $.each(bookings, function(index, booking) {
                                html += '<tr data-customer-id="' + booking.customer_id + '" data-customer-email="' + booking.customer_email + '" data-customer-phone="' + booking.customer_phone + '" data-service-id="' + booking.service_id + '" data-employee-id="' + booking.employee_id + '" data-status="' + booking.status + '" data-notes="' + (booking.notes || '') + '">';
                                html += '<td>' + booking.customer_name + '</td>';
                                html += '<td>' + booking.service_name + '</td>';
                                html += '<td>' + booking.appointment_date + '</td>';
                                html += '<td>' + booking.appointment_time + '</td>';
                                html += '<td><span class="booking-status booking-status-' + booking.status + '">' + booking.status + '</span></td>';
                                html += '<td>$' + booking.cost + '</td>';
                                html += '<td>';
                                html += '<button class="button edit-booking" data-id="' + booking.id + '">Edit</button> ';
                                html += '<button class="button delete-booking" data-id="' + booking.id + '">Delete</button>';
                                html += '</td>';
                                html += '</tr>';
                            });
                        }

                        $('#booking-list tbody').html(html);
                    } else {
                        $('#booking-list tbody').html('<tr><td colspan="7">Error loading bookings.</td></tr>');
                    }
                },
                error: function() {
                    $('#booking-list tbody').html('<tr><td colspan="7">Error loading bookings.</td></tr>');
                }
            });
        }

        function loadServices() {
            $.ajax({
                url: booking_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_services',
                    nonce: booking_admin_ajax.nonce
                },
                beforeSend: function() {
                    $('#service-list tbody').html('<tr><td colspan="5">Loading...</td></tr>');
                },
                success: function(response) {
                    if (response.success) {
                        var services = response.data;
                        var html = '';

                        if (services.length === 0) {
                            html = '<tr><td colspan="5">No services found.</td></tr>';
                        } else {
                            $.each(services, function(index, service) {
                                html += '<tr data-description="' + (service.description || '') + '">';
                                html += '<td>' + service.name + '</td>';
                                html += '<td>' + service.duration + ' min</td>';
                                html += '<td>$' + service.price + '</td>';
                                html += '<td>' + (service.description || '') + '</td>';
                                html += '<td>';
                                html += '<button class="button edit-service" data-id="' + service.id + '">Edit</button> ';
                                html += '<button class="button delete-service" data-id="' + service.id + '">Delete</button>';
                                html += '</td>';
                                html += '</tr>';
                            });
                        }

                        $('#service-list tbody').html(html);
                    } else {
                        $('#service-list tbody').html('<tr><td colspan="5">Error loading services.</td></tr>');
                    }
                },
                error: function() {
                    $('#service-list tbody').html('<tr><td colspan="5">Error loading services.</td></tr>');
                }
            });
        }

        function loadEmployees() {
            $.ajax({
                url: booking_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_employees',
                    nonce: booking_admin_ajax.nonce
                },
                beforeSend: function() {
                    $('#employee-list tbody').html('<tr><td colspan="4">Loading...</td></tr>');
                },
                success: function(response) {
                    if (response.success) {
                        var employees = response.data;
                        var html = '';

                        if (employees.length === 0) {
                            html = '<tr><td colspan="4">No employees found.</td></tr>';
                        } else {
                            $.each(employees, function(index, employee) {
                                html += '<tr>';
                                html += '<td>' + employee.name + '</td>';
                                html += '<td>' + (employee.email || '') + '</td>';
                                html += '<td>' + (employee.phone || '') + '</td>';
                                html += '<td>';
                                html += '<button class="button edit-employee" data-id="' + employee.id + '">Edit</button> ';
                                html += '<button class="button delete-employee" data-id="' + employee.id + '">Delete</button>';
                                html += '</td>';
                                html += '</tr>';
                            });
                        }

                        $('#employee-list tbody').html(html);
                    } else {
                        $('#employee-list tbody').html('<tr><td colspan="4">Error loading employees.</td></tr>');
                    }
                },
                error: function() {
                    $('#employee-list tbody').html('<tr><td colspan="4">Error loading employees.</td></tr>');
                }
            });
        }

        function loadCustomers() {
            $.ajax({
                url: booking_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_customers',
                    nonce: booking_admin_ajax.nonce
                },
                beforeSend: function() {
                    $('#customer-list tbody').html('<tr><td colspan="4">Loading...</td></tr>');
                },
                success: function(response) {
                    if (response.success) {
                        var customers = response.data;
                        var html = '';

                        if (customers.length === 0) {
                            html = '<tr><td colspan="4">No customers found.</td></tr>';
                        } else {
                            $.each(customers, function(index, customer) {
                                html += '<tr>';
                                html += '<td>' + customer.name + '</td>';
                                html += '<td>' + customer.email + '</td>';
                                html += '<td>' + (customer.phone || '') + '</td>';
                                html += '<td>';
                                html += '<button class="button edit-customer" data-id="' + customer.id + '">Edit</button> ';
                                html += '<button class="button delete-customer" data-id="' + customer.id + '">Delete</button>';
                                html += '</td>';
                                html += '</tr>';
                            });
                        }

                        $('#customer-list tbody').html(html);
                    } else {
                        $('#customer-list tbody').html('<tr><td colspan="4">Error loading customers.</td></tr>');
                    }
                },
                error: function() {
                    $('#customer-list tbody').html('<tr><td colspan="4">Error loading customers.</td></tr>');
                }
            });
        }

        function showMessage(type, message) {
            var messageDiv = $('.booking-message');
            messageDiv.html('<div class="booking-message ' + type + '">' + message + '</div>');

            // Auto-hide after 5 seconds
            setTimeout(function() {
                messageDiv.empty();
            }, 5000);
        }
    });

})(jQuery);