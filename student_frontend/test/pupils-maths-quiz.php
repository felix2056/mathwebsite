<?php require_once('header.php'); ?>
<?php require_once('sidebar.php'); ?>
<section class="content">
<div class="content__inner">
<!--new row-->
<div class="row">
   <section class="Maths-Quiz col-lg-8 col-sm-12 mb-4">
     <div id="grade" class="card mb-4">
     	<div class="card-body">
     		<h3>Woah! Well done</h3>
     		<p class="lead">You got 4 out 5 questions correct</p>
     		<h3><span class="badge badge-success">80%</span></h3>
     	</div>
     </div>
     
      <div id="studentanswers" class="card">
         <div class="card-body">
           <!--insert question from db here-->
            <p class="lead"><strong>Usually £1 = Rs 40</strong></p>
            <p>Allan changes <strong>£140</strong>. He goes to the local bazar and spends <strong>Rs 1200</strong> on goods.</p>
            <p>How much money does he have left in <strong>rupees? </strong> 
            <form>
               <div class="form-group">
                  <label for="answer" class="bg-danger text-white">Your Answer is incorrect</label>
                  <label for="answer" class="bg-success text-white">Your Answer is correct</label>
                  <div class="input-group mb-2 mr-sm-2">
                     <div class="input-group-prepend">
                        <div class="input-group-text">Rs</div>
                     </div>
                     <input type="number" class="form-control form-control-lg" disabled>
                  </div>
               </div>
               <div class="solution">
               	<p>Correct answer is <strong>32</strong></p>
               </div>
               <!--<button class="btn btn-primary btn-block btn-lg">Submit</button>-->
               <div class="solution"></div>
            </form>
            <!--end question from DB-->
         </div>
      </div>
   </section>
   <section id="questionList" class="col-lg-4 col-sm-12">
      <div class="card">
         <div class="card-body">
           <div class="scroll-element">
				<div class="list-group">
				   <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between answered"><span>Question 1</span><span class="icon tick "></span></a>
				   <a href="#" class="list-group-item list-group-item-action  d-flex justify-content-between answered"><span>Question 2</span><span class="icon tick"></span></a>
				   <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between answered"><span>Question 3 </span><span class="icon tick"></span></a>
				   <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between answered"><span>Question 4 </span><span class="icon tick"></span></a>
				   <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between answered"><span>Question 5 </span><span class="icon tick"></span></a>
				</div>
            </div>
            <button class="btn btn-success btn-lg btn-block mt-4">Get My Grade</button>
         </div>
      </div>
   </section>
   <!--/end row-->
</div>
<!---EVERYTHING BELOW HERE CAN GO IN footer.php-->
<?php require_once('footer.php'); ?>

