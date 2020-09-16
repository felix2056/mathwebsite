import Sidebar from "./Sidebar.js";

export default {
    template:`
    <div class="row">
         <section class="Maths-Questions col-8">
            <div class="card">
               <div class="card-body">
                    <div class="scroll-element">
                        <div class="list-group">
                            <h2>Start Quiz</h2>
                        </div>
                        <p>Start answering from the first question. All answers will be submitted, reviewed and scored after the quiz</p>
                    </div>
                  <!--end question from DB-->
               </div>
            </div>
         </section>
         
         <section id="MathsQuestionsList" class="col-4">
            <sidebar></sidebar>
         </section>`,

    components: { Sidebar },
  }