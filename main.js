/*
 * main.js
 * 
 * Main javasscript executed for Jade Delight's order page. Opens new 
 * receipt page where only foods ordered will display.
 * Edit: order page will also send email confirmation. 
 * 
 * Note: Original ordering form will clear after a valid submission.
 * 
 * Amy Bui
 * Comp20, Spring2021
 * Update: 4/12/21 --   Editted to work with php pages 
 *                      jade_delight.php and order.php
 */

// Defined variables for receipt
const TAX       = 0.0625;
var firstName   = "";
var lastName    = "";
var phoneNo     = "";
var getOption   = $("input[name='p_or_d']:checked").val();
var town        = "";   // city
var st          = "";   // street name
var waiting     = "";   // time until food is ready


/*
 * Automatically update order page as user selects food quantities
 * and chooses pickup/delivery.
 */
$(document).ready(
    function() {
        $("#error-order").hide();
        updateGetMethodsDisplay();
        updatePrices();
        updateGetMethod();
    }
);

/* restoreStyle
 *
 * Removes "required" class added as part of error message 
 * users see with invalid submissions, so they can see an updated display 
 * for their next submission attempt. 
 */
function restoreStyle() {
    inputNames = ["lname", "street", "city", "phone"];
    pKeyWord = ["Last Name", "Street", "City", "Phone"];
    for (let i = 0; i < inputNames.length; i++) {
        if ($("input[name='" + inputNames[i] + "']").hasClass("required")) {
            $("input[name='" + inputNames[i] + "']").removeClass("required");}
        
        if ($("p:contains(" + pKeyWord[i] + ")").hasClass("required")) {
            $("p:contains(" + pKeyWord[i] + ")").removeClass("required");}
    }
}

/* updatePrices
 *
 * Updates the Total Cost column of menue items as user updates
 * the quantity of each menu item they want, along with 
 * subtotal, tax, and total cost field.
 * 
 * Note: Only menu of food items are expected to correspond to 
 *      the selector elements referenced in this function, 
 *      ie the select tags with options selected, and 
 *      input tags with key name values of "cost". Uses set prices
 *      that were set in theMenu and foodKey arrays 
 *      (located in jade_delight.php).
 */
function updatePrices() {
    $("select").on("input", function() {
        var subTot = 0;
        $("select option:selected").each(function(idx) {
            let qty = $(this).val();
            let c = qty * theMenu[foodKey[idx]];

            // update each Total Cost Field
            $("input[name='cost[]']:eq(" + idx + ")").attr("value", (c).toFixed(2));
            subTot += c;
        });
        MassTax = subTot * TAX;

        // update Subtotal, Tax, and Total
        $("input[name='subtotal']").attr("value", (subTot).toFixed(2));
        $("input[name='tax']").attr("value", (MassTax).toFixed(2));
        $("input[name='total']").attr("value", (subTot + MassTax).toFixed(2));
    });
}

/* updateGetMethod
 *
 * Determines when the pickup/delivery method is changed. 
 * Updates display on form accordingly
 * 
 */
function updateGetMethod() {
    $("input[name='p_or_d']").on("change", function() {
        getOption = $("input[name='p_or_d']:checked").val();
        updateGetMethodsDisplay();
    });
}

/* updateGetMethodsDisplay
 *
 * Determines city/street dislpay status based on pickup/delivery option.
 */
function updateGetMethodsDisplay() {
    if (getOption == "pickup") {
        $(".optional-display").hide();
    } else {
        $(".optional-display").show();
    }
}

/* formatTime
* Returns a string that is some time additionalTime minutes from now. 
* Time is in 12-Hour format: HH:MM (AM/PM)
*
*/
function formatTime(additionalTime) {
    // get current time and calc. furture time. 
    var timeReady = new Date;
    timeReady.setMinutes(timeReady.getMinutes() + parseInt(additionalTime));

    // set 12 AM to be '12' and not '00'
    var hours = timeReady.getHours() % 12;
    hours = hours == 0 ? 12 : hours;

    // display two digit minute
    var minutes = timeReady.getMinutes();
    minutes = minutes < 10 ? '0' + minutes : minutes;

    return `${hours}:${minutes} ${timeReady.getHours() >= 12 ? 'PM' : 'AM'}`;
}


/* validatePhone
 *
 * Confirms phone number is 10 digits long, OR a 1 followed by 10 digits
 */
function validatePhone(number){
    // declare reg exp for 10/11 digit phone number pattern
    let phone = /^1?\d{10}$/;

    if (number.match(phone)) {
        return true;
    } else {
        $("input[name='phone'], p:contains(Phone)").addClass("required");
        return false;
    }
}

/* validateEmail
 *
 * Confirms valid email address using regex from https://www.w3resource.com/javascript/form/email-validation.php
 */
function validateEmail(eadd){
    // declare reg exp for 10/11 digit phone number pattern
    let email = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

    if (eadd.match(email)) {
        return true;
    } else {
        $("input[name='email'], p:contains(Email)").addClass("required");
        return false;
    }
}

/* titleCase
 *
 * Formats a string word to title case, ie capitalize the first letter. 
 * and returns it. I got the code from this site about setting cases 
 * with javascript/jquery:
 * cite: https://www.smartherd.com/convert-text-cases-using-jquery-without-css/
 */
function titleCase(word) {
    return word.toLowerCase().replace(/\b[a-z]/g, function(txtVal) {
        return txtVal.toUpperCase();
    });
}


/* checkFilled
 *
 * Confirms if content is not empty. Displays to user 
 * Red styling if content is empty.
 */
function checkFilled(content, conType) {
    if (content == "") {
        // Change style to show required fields
        $("input[name='" + conType + "']").addClass("required");

        if (conType == "lname") {
            $("p:contains(Last Name)").addClass("required");
        } else {
            $("p:contains(" + titleCase(conType) + ")").addClass("required");
        }

        return false;
    } else {
        return true;
    }

}


/* checkOrder
 *
 * Ensures user orders at least one item from menu.
 * 
 * Note: Only menu food items ar expected to be on form 
 *      and to correspond to select elements (in jade_delight.php)
 */
function checkOrder() {
    atLeastOne = false;
    $("select option:selected").each(function() {
        if ($(this).val() != 0)
            atLeastOne = true;    // at least one item is not 0 qty
    });

    if (!atLeastOne)    // display order requirement for 3 s.
        $("#error-order").fadeIn().delay(3000).fadeOut();

    return atLeastOne;
}

/*
 * Validate user input order information correctly. 
 * Must include: last name, phone (valid number), city/street (if delivery)
 * and at least 1 item ordered
 */
function validate() {
    restoreStyle();     // remove any error styling for next submission attempt

    lastName = $("input[name='lname']").val();
    phoneNo = $("input[name='phone']").val();
    email = $("input[name='email']").val();
    time = "";

    var phoneValid  = validatePhone(phoneNo);
    var emailValid  = validateEmail(email);
    var lnameValid  = checkFilled(lastName, "lname");
    var orderValid  = checkOrder();
    var cityValid   = true;
    var streetValid = true;

    // update street/city/time based on pickup or delivery option
    if (getOption == "delivery") {
        town = $("input[name='city']").val();
        st = $("input[name='street']").val();

        cityValid = checkFilled(town, "city");
        streetValid = checkFilled(st, "street");

        time = formatTime(30);
    } else {
        time = formatTime(15);
    }

    // Confirm all checks were valid.
    if (phoneValid && emailValid && lnameValid && orderValid && cityValid && streetValid) {
        $("input[name='time']").val(time);
        return true;
    } else {
        return false;
    }
}