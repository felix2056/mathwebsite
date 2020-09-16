<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8"/>
	<meta name="robots" content="noindex"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
	<link type="text/css" rel="stylesheet" href="css/my-css.css"/>
	<link type="text/css" rel="stylesheet" href="css/maths-quiz.css"/>
	<link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap/dist/css/bootstrap.min.css"/>

	<title>Writing Numbers</title>
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
														<!--Place value exercise set-->
														<div class="writing-numbers exercise-container">
															<span class="badge">Writing Numbers</span>
															<div class="exercise">
																<div class="exercise-header d-flex justify-content-between">
																	<h6 class="align-self-center">Write the number</h6>
																	<div class="actions">
																		<button class="btn btn-light">
                                                         <i class="icon trash"></i>
                                                      </button>
																	
																	</div>
																</div>
																<div class="exercise-body mb-3">
																	<ul class="d-flex flex-wrap" id="questions">
																		<!--question-->
																		<li>
																			<div>
																				<p>Eighteen</p>
																				<p><input type="number" class="form-control">
																				</p>
																			</div>
																		</li>
																		<li>
																			<div>
																				<p>Three hundred and sixteen</p>
																				<p><input type="number" class="form-control">
																				</p>
																			</div>
																		</li>
																		<li>
																			<div>
																				<p>3450</p>
																				<ul class="form-group">
																					<li class="custom-control custom-radio">
																						<input type="radio" class="custom-control-input" name="answer" value="3450" id="3450">
																						<label class="custom-control-label" for="3450">Three hundred and forty five</label>
																					</li>
																					<li class="custom-control custom-radio">
																						<input type="radio" class="custom-control-input" name="answer" value="0" id="0">
																						<label class="custom-control-label" for="0">Three thousand, four hundred and fifty</label>
																					</li>
																					<li class="custom-control custom-radio">
																						<input type="radio" class="custom-control-input" name="answer" value="0" id="00">
																						<label class="custom-control-label" for="00">Three hundred four thousand and fifty</label>
																					</li>
																				</ul>
																			</div>
																		</li>
																		<!--end question-->
																	</ul>
																</div>
															</div>
														</div>
														<!--Place value exercise set-->
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
															<input type="number" class="form-control mb-2" name="min" id="min" value="10">
														</div>
														<div class="col-sm-12 col-lg-6">
															<label>High Range</label>
															<input type="number" class="form-control mb-2" name="max" id="max" value="100">
														</div>
													</div>
												</template>
												<template>
													<div id="type" class="form-group">
														<label>Question Type</label>
														<div class="custom-control custom-radio">
															<input type="radio" class="custom-control-input" name="question-type" value="WN" id="WN" checked>
															<label class="custom-control-label" for="WN">Words to Numbers</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" class="custom-control-input" name="question-type" value="NW" id="NW">
															<label class="custom-control-label" for="NW">Numbers to Words</label>
														</div>
													</div>
												</template>
												<template>
													<div>
														<b-overlay :show="busy" rounded opacity="0.6" spinner-small spinner-variant="success" @hidden="onHidden">
															<b-button ref="button" :disabled="busy" block variant="success" size="lg" onClick="generate()">
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
	<script src="js/writing-numbers.js"></script>
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