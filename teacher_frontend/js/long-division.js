$(document).ready(() => {
  let generated;
  let quiz;

  generate();
});
function devide() {
  var input = {
    D: $("#D").val(),
    d: $("#d").val(),
  };
  $.ajax({
    type: "GET",
    url: "scripts/long-division/divide.php",
    data: input,
    success: function (result) {
      console.log(result);
      $("#result").html(populate(result, input));
    },
  });
}

function populateAnswre(result, input) {
  var html = "<table class='table table-bordered table-responsive'><tr>";

  result = JSON.parse(result);
  var ans = [" "].concat(result["ans"]);
  ans.forEach((i) => {
    console.log("haha:" + (i == " "));

    if (i == " ") {
      html = html + "<td>" + i + "</td>";
    } else {
      html = html + "<td class='border-bottom-thick-black'>" + i + "</td>";
    }
  });
  html =
    html + "</tr><tr><td class='border-right-thick'>" + input["d"] + "</td>";
  for (let i = 0; i < input["D"].length; i++) {
    const n = input["D"][i];
    html = html + "<td>" + n + "</td>";
  }

  html = html + "</tr><tr>";
  R = result["result"];
  for (let i = 0; i < R.length - 1; i++) {
    if (i % 2 == 0) {
      html = html + "<td>&minus;</td>";
    } else {
      html = html + "<td></td>";
    }

    for (let j = 0; j < R[i].length; j++) {
      let n = R[i][j];
      if (n == " ") {
        if (j != 0 && i % 2 == 0 && R[i][j - 1] != " ") {
          html = html + "<td> <span class='drop-arrow'>&darr;</span> </td>";
        } else {
          html = html + "<td>" + n + "</td>";
        }
      } else {
        if (i % 2 == 0) {
          html = html + "<td class='border-bottom-thick-gray'>" + n + "</td>";
        } else {
          html = html + "<td>" + n + "</td>";
        }
      }
    }
    html = html + "</tr><tr>";
  }
  html = html + "<td></td>";
  for (let j = 1; j < R[R.length - 1].length; j++) {
    const n = R[R.length - 1][j];
    html = html + "<td>" + n + "</td>";
  }
  html += "</tr></table>";

  return html;
}

function generate() {
  var input = {
    level: $('input[name="level"]:checked').val(),
    withRemainder: $('input[name="remainder"]:checked').val(),
    Nquestions: $("#Nquestions").val(),
  };
  $.ajax({
    type: "GET",
    url: "scripts/long-division/generate.php",
    data: input,
    success: function (result) {
      result = JSON.parse(result);
      console.log(result);

      setGenerated(result, input);
      $("#questions").html(populateQuestions(result));
    },
  });
}

function setGenerated(result) {
  generated = parseResults(result);
  console.log(generated)
}

function schedule() {
  Swal.mixin({
    title: "Schedule This Exercise Set?",
    text: "Commence scheduling and save this excercise to database!",
    icon: "info",
    showLoaderOnConfirm: true,
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Next &rarr;",
    progressSteps: ["1", "2", "3"],
  })
    .queue([
      {
        title: "Quiz Instructions!",
        text: "Insert basic instructions for this quiz",
        input: "textarea",
      },
      {
        title: "Quiz Total Marks!",
        text: "Insert total average score for this quiz (0-100)",
        input: "number",
      },
      {
        title: "Start & End Dates!",
        html:
          '<p>Insert date of the commencement of this quiz</p><input id="start_date" type="date" class="swal2-input"><p>Insert date of the conclusion of this quiz</p><input id="end_date" type="date" class="swal2-input">',
      },
    ])
    .then((result) => {
      if (result.value) {
        quiz = {};
        quiz.topic = "Long Division";
        quiz.instruction = result.value[0];
        quiz.total_marks = result.value[1];

        quiz.Nquestions = $("#Nquestions").val();
        quiz.start_date = $("#start_date").val();
        quiz.end_date = $("#end_date").val();

        console.log(quiz);

        $.ajax({
          type: "POST",
          url: "scripts/long-division/schedule.php",
          dataType: "json",
          data: { generatedData: generated, quizData: quiz },
          success: function (result) {
            if (result.code == "200") {
              Swal.fire({
                icon: "success",
                title: "Transaction Successful!",
                text: String(result.msg),
              });

              //result = JSON.parse(result);
              console.log(result);
            } else {
              Swal.fire({
                icon: "error",
                title: "Transaction Error",
                text: String(result.msg),
                footer: "<a href>Why do I have this issue?</a>",
              });

              // $(".display-error").html("<ul>"+data.msg+"</ul>");
              // $(".display-error").css("display","block");
            }
          },
        });
      }
    });
}

function populateQuestions(resultArray) {
  html = "";
  for (let index = 0; index < resultArray.length; index++) {
    const element = resultArray[index];
    html =
      html +
      `<li>
        <p> <span id='d_${index}'>${element["d"]}</span>
           <span id='D_${index}'>${element["D"]}</span>
        </p>
        <p class="mb-0">
           <span class="form-group d-flex justify-content-between">
              <label>Answer:</label>
              <input type="number" class="form-control" id='ans_${index}'>
              <label>R:</label>
              <input type="number" class="form-control" id='rem_${index}'>
           </span>
        </p>
        <b-button ref="button" :disabled="busy" block variant="success" size="lg" onclick="check('${index}')" style="
        cursor: pointer">
                                                Check
                                             </b-button>
        <div id='result_${index}'></div>
     </li>`;
  }
  return html;
}

function check(id) {
  var data = {
    D: $("#D_" + id).html(),
    d: $("#d_" + id).html(),
    rem: $("#rem_" + id).val(),
    ans: $("#ans_" + id).val(),
  };

  $.ajax({
    type: "GET",
    url: "scripts/long-division/check.php",
    data: data,
    success: function (result) {
      result = JSON.parse(result);
      console.log(result);
      if (result.correct) {
        $("#result_" + id).html("correct");
      } else {
        $("#result_" + id).html(populateAnswre(result.answer, data));
      }
    },
  });
}

function parseResults(resultArray) {
  let html = "";
  let response = [];

  for (let index = 0; index < resultArray.length; index++) {
    const element = resultArray[index];
    // html =
    //   html +
    //   `<li>
    //     <p> <span>${element["d"]}</span>
    //        <span>${element["D"]}</span>
    //     </p>
    //     <p class="mb-0">
    //        <span class="form-group d-flex justify-content-between">
    //           <label>Answer:</label>
    //           <input type="number" class="form-control">
    //           <label>R:</label>
    //           <input type="number" class="form-control">
    //        </span>
    //     </p>
    //  </li>`;

    html = element;

    let answers = [];

    var single = Math.floor(element["D"] / element["d"]);
    var rem = element["D"] % element["d"];

    answers.push([single, rem]);

    response.push({
        q: html, 
        ans: answers
    });
  }

  return response;
}
