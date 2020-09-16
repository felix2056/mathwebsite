<!DOCTYPE html>
<html lang="en">
   <head>
      <!-- Required meta tags -->
      <meta charset="utf-8"/>
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
      <link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap/dist/css/bootstrap.min.css"/>
      <link type="text/css" rel="stylesheet" href="css/my-css.css"/>
      <link type="text/css" rel="stylesheet" href="css/maths-quiz.css"/>
      <title>Roman Numbers</title>
   </head>
   <body>
      <main id="app" class="main">
         <section class="content">
            <div class="container-fluid">
               <!--new row-->
               <div class="row">
                  <section class="col-12">
                     <div class="card">
                        <div class="card-body">
                           <div class="row">
                              <div id="excerises" class="col-12 col-xl-2 mb-3">
                                 <?php include("sidemenu.php"); ?>
                              </div>
                              <!--stage-->
                              <div class="stage col-12 col-lg-8 col-xl-7 mb-3">
                                 <div class="card">
                                    <div class="card-body">
                                       <div class="questions-container">
                                          <form>
                                             <!--Roman Numbers exercise set-->
                                             <div class="roman-numbers exercise-container">
                                                <span class="badge">Roman Numbers</span>
                                                <div class="exercise">
                                                   <div class="exercise-header d-flex justify-content-between">
                                                      <h6 class="align-self-center">Solve the following</h6>
                                                      <div class="actions">
                                                         <button class="btn btn-light">
                                                         <i class="icon trash"></i>
                                                         </button>
                                                      </div>
                                                   </div>
                                                   <div class="exercise-body mb-3">
                                                      <ul class="d-flex flex-wrap" id="questions">
                                                         <!--question-->
                                                         <!--end question-->
                                                      </ul>
                                                   </div>
                                                </div>
                                             </div>
                                             <!--Roman Numbers exercise set-->
                                          </form>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <!--end stage-->
                              <div id="options" class="col-12 col-lg-4 col-xl-3">
                                 <div class="card">
                                    <div class="card-body">
                                       <div class="form-group flex-fill justify-content-between">
                                          <label class="d-inline-flex">Number of Questions</label>
                                          <select class="d-inline-flex custom-select" id="Nquestions">
                                             <option value="5">5</option>
                                             <option value="10">10</option>
                                             <option value="15">15</option>
                                          </select>
                                       </div>
                                       <template>
                                          <div class="form-row form-group">
                                             <div class="col-sm-12 col-lg-6">
                                                <label>Low Range</label>
                                                <input type="number" class="form-control mb-2" name="min" id="min" value = 1>
                                             </div>
                                             <div class="col-sm-12 col-lg-6">
                                                <label>High Range</label>
                                                <input type="number" class="form-control mb-2" name="max" id="max" value = 100>
                                             </div>
                                          </div>
                                       </template>
                                       <template>
                                          <div id="level" class="form-group">
                                             <label>Question Type</label>
                                             <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="question-type" value="rm" id="rm" checked>
                                                <label class="custom-control-label" for="rm">Roman to Modern</label>
                                             </div>
                                             <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="question-type" value="mr" id="mr">
                                                <label class="custom-control-label" for="mr">Modern to Roman</label>
                                             </div>
                                          </div>
                                       </template>
                                       <template>
                                          <div>
                                             <b-overlay :show="busy" rounded opacity="0.6" spinner-small spinner-variant="success" @hidden="onHidden">
                                                <b-button ref="button" :disabled="busy" block variant="success" size="lg" onclick="generate();">
                                                   Generate Questions
                                                </b-button>
                                             </b-overlay>
                                          </div>
                                       </template>

                                       <template>
													<div style="margin-top: 10px;">
														<b-overlay rounded opacity="0.6" spinner-small spinner-variant="success">
															<b-button ref="button" block variant="danger" size="lg" onclick="schedule()">
																Schedule
															</b-button>
														</b-overlay>
													</div>
												</template>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </section>
                  <!--/end row-->
               </div>
            </div>
         </section>
      </main>
      <!-- Javascript -->
      <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
      <script src="js/roman-numbers.js"></script>
      <!-- Vue -->
      <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
      <!-- Load Vue followed by BootstrapVue -->
      <script src="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.js"></script>
      <!-- Sweet Alert JS -->
	   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
      <script>
         var app = new Vue( {
         	el: "#app",
         } );
      </script>
   </body>
</html>

