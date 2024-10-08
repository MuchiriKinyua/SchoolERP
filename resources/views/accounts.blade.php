@extends('layouts.app') 

@section('content') 
    <div class="container">
        <h1>Accounts Page</h1>
        <form id="paymentForm">
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" class="form-control" id="firstName" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" class="form-control" id="lastName" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="phoneNumber">Phone Number</label>
                <input type="text" class="form-control" id="phoneNumber" name="phone_number" required>
            </div>
            <div class="form-group">
                <label for="accountNumber">Account Number</label>
                <input type="text" class="form-control" id="accountNumber" name="account_number" required>
            </div>
            <button type="submit" class="btn btn-primary">Pay</button>
        </form>

        <div id="message" class="mt-3"></div>
    </div>

    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting the traditional way
            
            // Get form data
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const phoneNumber = document.getElementById('phoneNumber').value;
            const accountNumber = document.getElementById('accountNumber').value;

            // Simple validation
            if (firstName && lastName && phoneNumber && accountNumber) {
                // Assume payment is always successful for demonstration
                document.getElementById('message').innerHTML = '<div class="alert alert-success">Payment Successful!</div>';
            } else {
                document.getElementById('message').innerHTML = '<div class="alert alert-danger">Please provide valid details.</div>';
            }

            // Optionally clear the form
            this.reset();
        });
    </script>
@endsection
