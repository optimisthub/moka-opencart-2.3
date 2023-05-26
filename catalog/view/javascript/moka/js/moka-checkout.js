(function() {
    var creditCardNumberInput = document.getElementById("moka-checkout-card-number");
    var expirationInput = document.getElementById("moka-checkout-expire-date");
    var ccvInput = document.getElementById("moka-checkout-cvc");
    var visaIcon = document.getElementsByClassName("moka-checkout-visa-icon");
    var mastercardIcon = document.getElementsByClassName("moka-checkout-mastercard-icon");
    var amexIcon = document.getElementsByClassName("moka-checkout-amex-icon");
    var troyIcon = document.getElementsByClassName("moka-checkout-troy-icon");
    var cardFamilies = [{
            name: "Visa",
            pattern: /^4/,
            icon: visaIcon
        },
        {
            name: "Mastercard",
            pattern: /^(51|52|53|54|55|58)/,
            icon: mastercardIcon
        },
        {
            name: "American Express",
            pattern: /^(34|37)/,
            icon: amexIcon
        },
        {
            name: "Troy",
            pattern: /^(?:9|65|36)/,
            icon: troyIcon
        },
    ];

    function formatExpirationDate(input) {
        var value = input.value.replace(/\D/g, "").substring(0, 6);
        var month = value.substring(0, 2);
        var year = value.substring(2, 6);

        if (value.length > 2) {
            input.value = month + "/" + year;
        } else {
            input.value = value;
        }
    };

    function handleBackspace(input) {
        var value = input.value.replace(/\D/g, "").substring(0, 6);
        var month = value.substring(0, 2);
        var year = value.substring(2, 6);

        if (value.length === 2) {
            input.value = month;
        } else if (value.length === 3) {
            input.value = month + "/" + year.substring(0, 1);
        } else if (value.length > 3) {
            input.value = month + "/" + year;
        } else {
            input.value = value;
        }
    }

    function expirationUpdateValue(input) {
        var value = input.value.replace(/\D/g, "").substring(0, 6);
        var month = value.substring(0, 2);
        var year = value.substring(2, 6);

        if (value.length >= 2) {
            input.value = month + "/" + year;
        } else {
            input.value = value;
        }
    }

    function getCardFamily(creditCardNumber) {
        for (var i = 0; i < cardFamilies.length; i++) {
            var cardFamily = cardFamilies[i];
            if (cardFamily.pattern.test(creditCardNumber)) {
                return cardFamily;
            }
        }

        return null;
    }

    function checkCardFamily(input) {
        var creditCardNumber = input.value.replace(/\D/g, "");
        var cardFamily = getCardFamily(creditCardNumber);

        for (var i = 0; i < visaIcon.length; i++) {
            visaIcon[i].style.display = "none";
        }
        for (var i = 0; i < mastercardIcon.length; i++) {
            mastercardIcon[i].style.display = "none";
        }
        for (var i = 0; i < amexIcon.length; i++) {
            amexIcon[i].style.display = "none";
        }
        for (var i = 0; i < troyIcon.length; i++) {
            troyIcon[i].style.display = "none";
        }

        if (cardFamily) {
            for (var i = 0; i < cardFamily.icon.length; i++) {
                cardFamily.icon[i].style.display = "block";
            }
        }

        if (creditCardNumber === "") {
            for (var i = 0; i < cardFamilies.length; i++) {
                var icons = cardFamilies[i].icon;

                for (var j = 0; j < icons.length; j++) {
                    icons[j].style.display = "block";
                }
            }
        }
    }

    function formatCreditCardNumber(input) {
        var value = input.value.replace(/\D/g, "").substring(0, 16);
        var formattedValue = "";

        for (var i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formattedValue += " ";
            }
            formattedValue += value.charAt(i);
        }

        input.value = formattedValue;
    }

    function formatCCV(input) {
        var value = input.value.replace(/\D/g, "").substring(0, 4);
        input.value = value;
    }

    creditCardNumberInput.addEventListener("input", function(event) {
        formatCreditCardNumber(event.target);
        checkCardFamily(event.target);
    });

    expirationInput.addEventListener("input", function(event) {
        formatExpirationDate(event.target);
    });

    expirationInput.addEventListener("keyup", function(event) {
        if (event.key === "Backspace" || event.key === "Delete") {
            var value = event.target.value.replace(/\D/g, "");
            if (value.length === 2 || value.length === 3) {
                handleBackspace(event.target);
            } else if (value.length === 4) {
                expirationUpdateValue(event.target);
            }
        }
    });

    ccvInput.addEventListener("input", function(event) {
        formatCCV(event.target);
    });
})(window);