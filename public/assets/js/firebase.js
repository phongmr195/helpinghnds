// Handle send otp code to phone number
// var results;

// function sendVerificationCode() {
//     var phoneNumber = document.getElementById('phone_number').value;
//     firebase.auth().signInWithPhoneNumber(phoneNumber, window.recaptchaVerifier).then(function(confirmationResult){
//         window.confirmationResult = confirmationResult;
//         results = confirmationResult;
//         console.log(results);
//         alert('Send otp to phone success');
//     }).catch(function(err){
//         alert(err.message);
//     });
// }