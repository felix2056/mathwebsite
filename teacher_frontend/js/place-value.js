$(document).ready(() => {
    let generated;
    let quiz;
    
    generate();
});
function generate() {

    var input = {
        'level': $('input[name="level"]:checked').val(),
        'Nquestions': $('#Nquestions').val()
    }
    $.ajax({
        type: 'GET',
        url: "scripts/place-value/generate.php",
        data: input,
        success: function (result) {
            result = JSON.parse(result);
            console.log(result);

            setGenerated(result, input);
            $('#questions').html(populateQuestions(result));
        }
    });
}

function setGenerated(result) {
    generated = result;
}

function schedule() {
    Swal.mixin({
        title: 'Schedule This Exercise Set?',
        text: "Commence scheduling and save this excercise to database!",
        icon: 'info',
        showLoaderOnConfirm: true,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Next &rarr;',
        progressSteps: ['1', '2', '3']
      }).queue([
        {
            title: 'Quiz Instructions!',
            text: 'Insert basic instructions for this quiz',
            input: 'textarea'
        },
        {
            title: 'Quiz Total Marks!',
            text: 'Insert total average score for this quiz (0-100)',
            input: 'number'
        },
        {
            title: 'Start & End Dates!',
            html: '<p>Insert date of the commencement of this quiz</p><input id="start_date" type="date" class="swal2-input"><p>Insert date of the conclusion of this quiz</p><input id="end_date" type="date" class="swal2-input">'
        }
      ]).then((result) => {
        if (result.value) {
            quiz = {};
            quiz.topic = 'Place Value';
            quiz.instruction = result.value[0];
            quiz.total_marks = result.value[1];

            quiz.Nquestions = $('#Nquestions').val();
            quiz.start_date = $('#start_date').val();
            quiz.end_date = $('#end_date').val();
            
            console.log(quiz);

            $.ajax({
                type: 'POST',
                url: "scripts/place-value/schedule.php",
                dataType: "json",
                data: { generatedData: generated, quizData: quiz },
                success: function (result) {
                    if (result.code == "200"){
                        Swal.fire({
                            icon: 'success',
                            title: 'Transaction Successful!',
                            text: String(result.msg)
                        })

                        //result = JSON.parse(result);
                        console.log(result);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Transaction Error',
                            text: String(result.msg),
                            footer: '<a href>Why do I have this issue?</a>'
                        })
            
                        // $(".display-error").html("<ul>"+data.msg+"</ul>");
                        // $(".display-error").css("display","block");
                    }
                }
            });
        }
    })
}

function populateQuestions(result) {

    var html = '<ul class="d-flex flex-wrap">'

    for (let j = 0; j < result.length; j++) {
        const question = result[j];
        var q = question["q"].toString();
        var place = question['place'];
        var ans = question['ans'];

        html = html + '<li><span>'
        for (let i = 0; i < q.length; i++) {
            var number = q[i];
            if (i == place - 1) {
                html = html + `<u>${number}</u>`;
            } else {
                html = html + `${number}`;
            }
        }
        html = html + `</span><span> = </span>`;
        for (let i = ans.length - 1; i >= 0; i--) {
            html = html + `<div class="custom-control custom-radio custom-control-inline">
            <input type="radio" class="custom-control-input" id="q_${j}_${i}" name="placevalue-q_${j}" onchange="check('${j}')" value='${i}' place='${ans.length - place}'>
            <label class="custom-control-label" for="q_${j}_${i}">${ans[i]}</label>
         </div>`
        }
        html = html + `<div id="ans_${j}"></div>`

        html = html + '</li>'
    }
    html = html + '</ul>'
    return html;
}

function check(q) {
    var checked = $(`input[name="placevalue-q_${q}"]:checked`);
    var answer = checked.val();
    var Canswer = checked.attr('place');
    if (answer == Canswer) {
        $('#ans_' + q).html('<p style="color:green">Correct</p>');
    } else {
        var ans = $(`label[for="q_${q}_${Canswer}"]`).html();
        console.log(ans);
        $('#ans_' + q).html('<p style="color:red">' + ans + '</p>');
    }
}