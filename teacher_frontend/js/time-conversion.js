$(document).ready(() => {
    let generated;
    let quiz;

    generate();
});
function generate() {
    var input = {
        'Nquestions': $('#Nquestions').val(),
        'type': $('input[name="question-type"]:checked').val(),
    }
    console.log(input);
    $.ajax({
        type: 'GET',
        url: "scripts/time-conversion/generate.php",
        data: input,
        success: function (result) {
            if (result == '0') {
                alert('Houston, we have a problem!');
            } else {
                result = JSON.parse(result);
                console.log(result);

                setGenerated(result, input);
                $('#questions').html(populateQuestions(result));
                $(".solution").hide();
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
            quiz.topic = 'Time Conversion';
            quiz.instruction = result.value[0];
            quiz.total_marks = result.value[1];

            quiz.Nquestions = $('#Nquestions').val();
            quiz.start_date = $('#start_date').val();
            quiz.end_date = $('#end_date').val();
            
            console.log(quiz);

            $.ajax({
                type: 'POST',
                url: "scripts/time-conversion/schedule.php",
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
    var html = '';

    for (let i = 0; i < result.length; i++) {

        var question = result[i]['q'];
        var answer = result[i]['a'];


        html = html + `<li>`;
        console.log(question);
        html = html + question;


        var qq = "";
        for (let j = 0; j < answer.length; j++) {
            const ANS = answer[j];
            qq += `
    <div class="input-group ml-2">
    <input type="text" class="form-control"  id="q_${i}_${j}" ans='${answer[j].split(" ")[0]}' >
    <div class="input-group-append"><div class="input-group-text">`+ answer[j].split(" ")[1] + `</div>
    </div></div>`;


        }



        qq += `</div>
     <b-button ref="button" :disabled="busy" block variant="success" size="lg" onclick="check('${i}');">
            Check
         </b-button>`;
        html = html.replace("<q1>", qq);


        html = html.replace("<op-ch>", ` <input type="text" class="form-control ml-2"  id="q_${i}" ans='${answer[0]}'>
        <div class="form-group ml-2">
           <div class="custom-control custom-radio">
              <input type="radio" class="custom-control-input" name="ampm_${i}" value="AM" id="am_${i}">
              <label class="custom-control-label" for="am_${i}">AM</label>
           </div>
           <div class="custom-control custom-radio">
              <input type="radio" class="custom-control-input" name="ampm_${i}" value="PM" id="pm_${i}">
              <label class="custom-control-label" for="pm_${i}">PM</label>
           </div>
        </div>
        </div>
     <b-button ref="button" :disabled="busy" block variant="success" size="lg" onclick="check_opt('${i}');">
            Check
         </b-button>
        `);

        html = html.replace("<ID>", `ans_${i}`);


    }

    return html;
}

function check_opt(q){

    inputs = $('#q_' + q + ' :input');
    ans = $('#q_' + q.toString()).val();
    ans_2 =  $('input[name="ampm_'+q+'"]:checked').val()
    cans = $('#q_' + q.toString()).attr('ans');
    console.log(ans, cans);
    $.ajax({
        type: 'GET',
        url: "scripts/time-conversion/check.php",
        data: {
            'ans': ans+" "+ans_2,
            'cans': cans
        },
        success: function (result) {
            if (result == 'true') {
                $('#q_' + q).css('background', 'rgba(0, 255, 0, .1)');
            } else {
                $('#q_' + q).css('background', 'rgba(255, 0, 0, .1)');
                $('#q_' + q.toString()).val(cans.split(" ")[0]);
                if(cans.split(" ")[1]=="AM"){
                    $("#am_"+q).prop("checked", true);
                }else{
                    $("#pm_"+q).prop("checked", true);
                }
            }
        }
    });
}

function check(q) {

    for (let i = 0; i < 2; i++) {
        inputs = $('#q_' + q + ' :input');
        ans = $('#q_' + q.toString() + "_" + i).val();
        cans = $('#q_' + q.toString() + "_" + i).attr('ans');
        console.log(ans, cans);
        $.ajax({
            type: 'GET',
            url: "scripts/time-conversion/check.php",
            data: {
                'ans': ans,
                'cans': cans
            },
            success: function (result) {
                if (result == 'true') {
                    $('#q_' + q + "_" + i).css('background', 'rgba(0, 255, 0, .1)');
                } else {
                    $('#q_' + q + "_" + i).css('background', 'rgba(255, 0, 0, .1)');
                    a = $('#q_' + q.toString() + "_" + i).attr('ans');
                    equ = $('#q_' + q.toString() + "_" + i).attr('equ');
                    $('#q_' + q.toString() + "_" + i).val(a);
                }
            }
        });
    }
}