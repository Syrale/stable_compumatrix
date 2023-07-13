var data_message = document.getElementById("message");
var fetch_button = document.getElementById("get_data");
var user_info = document.getElementById("user_info");

fetch_button.addEventListener("click", function (e) {
  var user_input_data = document.getElementById("input_data").value;
  get_data(user_input_data);
});

function get_data(user_input_data) {
  $.ajax({
    type: "GET",
    url: "./api/index.php?id=" + user_input_data,
    success: function (result) {
      console.log("response:", result);

      try {
        var parsedResult = JSON.parse(result);
        console.log("parsedResult:", parsedResult);

        if (Array.isArray(parsedResult) && parsedResult.length > 0) {
          var userData = parsedResult[0];

          if (
            userData &&
            userData.id &&
            userData.name &&
            userData.wallet &&
            userData.inventory
          ) {
            var accountName = userData.name;
            var wallet = userData.wallet;
            var inventoryHTML = userData.inventory;

            user_info.innerHTML =
              "Account Name: " + accountName + "<br>" + "Wallet: " + wallet;

            data_message.innerHTML = inventoryHTML;
          } else {
            data_message.innerHTML = "Invalid response format.";
          }
        } else {
          data_message.innerHTML = "Empty or invalid response.";
        }
      } catch (error) {
        console.log("Parsing error:", error);
        data_message.innerHTML = "Invalid response.";
      }
    },
    error: function (error) {
      console.log("Error:", error.responseText);
      data_message.innerHTML = "An error occurred while retrieving the data.";
    },
  });
}
