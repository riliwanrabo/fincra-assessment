<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Checkout Demo</title>
</head>

<body>
    <form id="payment-form">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" required />
        </div>
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="text" id="amount" required />
        </div>
        <div class="form-group">
            <label for="name"> Name</label>
            <input type="text" id="name" />
        </div>
        <div class="form-group">
            <label for="phone"> Phone</label>
            <input type="text" id="phoneNumber" required />
        </div>
        <div class="form-submit">
            <button type="submit" onclick="payFincra"> Pay </button>
        </div>
    </form>
    <script src="https://unpkg.com/@fincra-engineering/checkout@2.2.0/dist/inline.min.js"></script>

    <script>
        const paymentForm = document.getElementById('payment-form');
        paymentForm.addEventListener("submit", payFincra, false);

        function payFincra(e) {
            e.preventDefault();
            Fincra.initialize({
                key: "{{ env('FINCRA_PUBLIC_KEY') }}",
                amount: Number(document.getElementById("amount").value),
                currency: "NGN",
                customer: {
                    name: document.getElementById("name").value,
                    email: document.getElementById("email").value,
                    phoneNumber: document.getElementById("phoneNumber").value,
                },
                //Kindly chose the bearer of the fees
                feeBearer: "business" || "customer",

                onClose: function() {
                    alert("Transaction was not completed, window closed.");
                },
                onSuccess: function(data) {
                    const reference = data.reference;
                    alert("Payment complete! Reference: " + reference);
                },
            });
        }
    </script>
</body>

</html>
