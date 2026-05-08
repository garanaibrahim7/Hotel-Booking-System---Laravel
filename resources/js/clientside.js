import $ from 'jquery';
window.$ = window.jQuery = $;

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

import flatpickr from 'flatpickr';
window.flatpickr = flatpickr;

import Swiper from 'swiper';
window.Swiper = Swiper;

import select2 from 'select2';
select2();

import './clientCoreScripts';

$(document).ready(function () {
    const initSelect2 = () => {
        $('.select2-initial').each(function () {
            $(this).select2({
                theme: 'bootstrap-5',
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder') || 'Select an option',
                allowClear: true,
                dropdownParent: $(this).closest('.modal, .offcanvas').length ? $(this).closest('.modal, .offcanvas') : $(document.body)
            });
        });
    }
    initSelect2();
});



$(document).on('click', '.remove-from-cart', function (e) {
    e.preventDefault();

    let btn = $(this);
    let url = btn.data('url');
    let itemContainer = btn.closest('.d-flex.align-items-center.bg-light');

    $.ajax({
        url: url,
        method: "GET",
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function (response) {
            if (response.success) {
                itemContainer.fadeOut(300, function () {
                    $(this).remove();

                    if (response.cart_count == 0) {
                        bootstrap.Offcanvas.getInstance(document.getElementById('cartDrawer')).hide();
                        $('#floating-cart').fadeOut();
                    }
                });

                $('#cart-count').text(response.cart_count);

                $('.total-amount-display').text('₹' + response.total_amount);
            }
        }
    });
});

$(document).on('click', '.add-to-cart-btn', function (e) {
    e.preventDefault();

    let btn = $(this);
    let form = btn.closest('.add-to-cart-form');

    let actionUrl = form.data('url');
    let formData = form.serialize();

    btn.prop('disabled', true).text('Adding...');

    $.ajax({
        url: actionUrl,
        method: "POST",
        data: formData,
        success: function (response) {
            console.log("Success Response:", response);

            if (response.show_modal) {
                $('#dateModal').modal('show');
            } else {
                $('#cartDrawer').addClass('show');

                btn.prop('disabled', false).text('Choose to Book');

                alert("Room added to cart!");
            }
        },
    });
});


document.addEventListener('DOMContentLoaded', function () {
    const fp = flatpickr("#flatpickr_input", {
        mode: "range",
        minDate: "today",
        dateFormat: "Y-m-d",
        defaultDate: [
            "{{ session('booking_check_in') }}",
            "{{ session('booking_check_out') }}"
        ],
        onClose: function (selectedDates, dateStr, instance) {

            if (selectedDates.length === 2) {
                const checkIn = instance.formatDate(selectedDates[0], "Y-m-d");
                const checkOut = instance.formatDate(selectedDates[1], "Y-m-d");

                document.getElementById('final_check_in').value = checkIn;
                document.getElementById('final_check_out').value = checkOut;

                document.getElementById('hiddenDateForm').submit();
            }
        }
    });

    // document.getElementById('dateEditTrigger').addEventListener('click', () => {
    //     fp.open();
    // });
});
