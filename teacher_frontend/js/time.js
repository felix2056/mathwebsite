$(document).ready(() => {
    let generated;
    let quiz;

    generate();
});
function generate() {
    var input = {
        'Nquestions': $('#Nquestions').val(),
        'operation': $('input[name="operation"]:checked').val(),
        'level': $('input[name="level"]:checked').val(),
    }
    console.log(input);
    $.ajax({
        type: 'GET',
        url: "scripts/time/generate.php",
        data: input,
        success: function (result) {
            if (result == '0') {
                alert('Houston we have a problem!');
            } else {
                result = JSON.parse(result);
                console.log(result);

                setGenerated(result, input);
                $('#questions').html(populateQuestions(result));
            }

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
            quiz.topic = 'Time';
            quiz.instruction = result.value[0];
            quiz.total_marks = result.value[1];

            quiz.Nquestions = $('#Nquestions').val();
            quiz.start_date = $('#start_date').val();
            quiz.end_date = $('#end_date').val();
            
            console.log(quiz);

            $.ajax({
                type: 'POST',
                url: "scripts/time/schedule.php",
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

function populateQuestions(result, mixed = false) {
    var html = ''
    for (let i = 0; i < result.length; i++) {

        var question = result[i]['q'];
        var answer = result[i]['a'];


        html = html + `<li>`;
        question = question.split("choice").join(`mC${i}`);
        question = question.split("opt").join(`opt_${i}_`);
        console.log(question);
        html = html + question;
        html = html.replace("<q1>", `<input type="text" class="form-control" id="q_${i}1" ans='${answer[0]}'' >`);
        html = html.replace("<q2>", `<input type="text" class="form-control" id="q_${i}2" ans='${answer[1]}' '>`);
        html = html.replace("<q3>", `<input type="text" class="form-control" id="q_${i}3" ans='${answer[2]}''>`);
        html = html.replace("<q4>", `<input type="text" class="form-control" id="q_${i}4" ans='${answer[3]}''>`);
        html = html.replace("<q5>", `<input type="text" class="form-control" id="q_${i}5" ans='${answer[4]}''>
         </li>`);
        
        // btn btn-secondary mt-3
        html = html.replace("<chbtn>", `<b-button class="btn btn-secondary mt-3" ref="button" :disabled="busy" block variant="success" size="lg" onclick="check(${i})">
        check
     </b-button>`);
        html = html.replace("<sol>", `sol_${i}`);        


    }

    return html;
}


function check(q) {
    for (let i = 0; i < 5; i++) {
        inputs = $('#q_' + q + ' :input');
        ans = $('#q_' + q.toString() + (i + 1)).val();
        cans = $('#q_' + q.toString() + (i + 1)).attr('ans');
        console.log(ans, cans);
        $.ajax({
            type: 'GET',
            url: "scripts/time/check.php",
            data: {
                'ans': ans,
                'cans': cans
            },
            success: function (result) {
                //console.log(result);
                if (result == 'true') {
                    $('#q_' + q + (i + 1)).css('background', 'rgba(0, 255, 0, .1)');
                } else {
                    $('#q_' + q + (i + 1)).css('background', 'rgba(255, 0, 0, .1)');
                    a = $('#q_' + q.toString()+ (i + 1)).attr('ans');
                    equ = $('#q_' + q.toString()+ (i + 1)).attr('equ');

                    $('#q_' + q.toString()+ (i + 1)).val(a);
                    $('#ans_' + q.toString() + '1').html($('#ans_' + q.toString() + (i+1)).attr('ans'));
                    $('#sol_'+ q.toString()).show();
                    
                }
            }
        });
    }
}