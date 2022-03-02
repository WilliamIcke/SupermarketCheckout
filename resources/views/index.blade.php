@php
    use App\Http\Controllers\CheckoutController;
@endphp
@extends('layouts.master')

@section('content')

<form id="SupermarketCheckoutForm">
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th>Item</th> 
                <th>Item Price</th>  
                <th>Special Offers</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach (App\Http\Controllers\CheckoutController::SKUs as $iItemName => $iItemDetails)
                <tr>
                    <td>
                        <label for={{"SKU-" . $iItemName . "-Input"}}>{{$iItemName}}</label>
                    </td>
                    <td>
                        <label for={{"SKU-" . $iItemName . "-Input"}}>{{$iItemDetails["UnitPrice"]}}</label>
                    </td>
                    <td>
                        @foreach ($iItemDetails["SpecialPrice"] as $iSpecialDetails)
                            <ul style="list-style: none; padding: 0;">
                                <li>
                                    @switch($iSpecialDetails["Type"])
                                        @case(App\Http\Controllers\CheckoutController::PURCHASED_WITH)
                                            <label for={{"SKU-" . $iItemName . "-Input"}}>{{$iSpecialDetails["Price"]. " when purchased with ". $iSpecialDetails["SpecialCondition"]}}</label>
                                            @break
                                        @case(App\Http\Controllers\CheckoutController::QUANTITY_BASED)
                                        @default
                                            <label for={{"SKU-" . $iItemName . "-Input"}}>{{$iSpecialDetails["SpecialCondition"]. " for ". $iSpecialDetails["Price"]}}</label>
                                    @endswitch
                                </li>
                            </ul>
                        @endforeach
                    </td>
                    <td>
                        <input class="quantity" id={{"SKU-" . $iItemName . "-Input"}} min="0" value="0"></input>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <button type="submit" class="btn btn-primary mb-2">Calculate cost</button>
</form>

@endsection

@section ('scripts')
    <script>
        $('#SupermarketCheckoutForm').on('submit', function (e) { // Listen for submit button click and form submission.
            e.preventDefault(); // Prevent the form from submitting
            let CheckoutData = {
                    "A": $('#SKU-A-Input').val(),
                    "B": $('#SKU-B-Input').val(),
                    "C": $('#SKU-C-Input').val(),
                    "D": $('#SKU-D-Input').val(),
                    "E": $('#SKU-E-Input').val(),
            };

            if (CheckoutData.length !== 0) { // If input is not empty.
			// Set CSRF token up with ajax.
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({ // Pass data to backend
                    type: "POST",
                    url: "/calculate-and-get-total",
                    data: {'keywordInput': CheckoutData},
                    success: function (response) {
                        // On Success, build a data table with keyword and densities
                        if (response.length > 0) {
                            let html = "<h4>Total cost of previous transaction:</h4><p>"+response+"</p>";
                            $('#SupermarketCheckoutForm').after(html);
                        }
                    },
                });
            }
        })
    </script>
@endsection