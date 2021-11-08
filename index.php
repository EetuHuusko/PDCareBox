<?php
include ("helpers_pd.php");
$metaItems = parseMetaData();
include ("auth.php");
$pid = "5fb26bc3e762c";
$qpid = $db -> quote($pid);

$p = $db -> select("SELECT * FROM problems WHERE problem_id = $qpid");

/*$characs = db_charasteristics($pid);*/

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>PDCareBox</title>

    <meta name="description" content="Crowdsourced decision support system." />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <!-- Bootstrap core CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.8.5/css/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/styles.css?o=<?php echo date('s'); ?>" />
    <link rel="stylesheet" href="./styles.css?o=<?php echo date('s'); ?>" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <link rel="manifest" href="./manifest.json" />
</head>

<body class="bg-faded">
    <div class="jumbotron jumboptron-fluid text-center jumbo-header">
        <div class="container">
            <img alt="PDCareBox Logo" id="PDlogo" src="img/PDCBlogo_beta.png" class="img-fluid">
            <h3 class="mt-4">Find new techniques for dealing with Parkinson's disease in your daily life...</h3>
            <p>...by tapping into community-based knowledge by over 300 patients and almost 30,000 unique opinions!</p>
            <!--<a href="#criteriasliders" id="jumpToButton" class="btn btn-xl rounded-pill mt-4">Start discovering</a>-->
        </div>
    </div>
    <section>
        <div class="container">
            <div class="col-lg-12">
            <div class="row align-items-middle">
                <div class="col-lg-5 p-4 order-lg-2 text-photo-div">
                    <img alt="Coffee cup" id="textPhoto" class="img-fluid" src="img/coffee_cup_and_hands.jpg"/>
                </div>
                <div id="intro" class="d-flex col-lg-7 p-4 order-lg-1 order-first">
                    <div class="row">
                        <h3 class="mt-2"><i class="fas fa-users"></i> What is PDCareBox?</h3>
                        <p>PDCareBox helps you find self-care techniques to deal with Parkinson’s disease in your daily life.
                        The techniques have been contributed by PD patients and their caretakers all over the world.
                        </p>
                        <h3 class="mt-1"><i class="far fa-question-circle"></i> How to use?</h3>
                        <p>Use the sliders below to select the criteria you want the techniques to follow.
                        The techniques will be shown after pushing the "Discover best matches" -button.
                        You can reset the selected criteria by clicking the "Reset criteria" -button.
                        <br><br>Select at least one criteria to find techniques. The criteria will only be applied to the search if the slider has been moved.
                        </p>
                        <h3 class="mt-1"><i class="fas fa-bars"></i> What do the results mean?</h3>
                        <p>The shown techniques will be the ten closest matches based on the criteria selected below.
                        The closeness is calculated by matching your desired criteria to the assessment data donated by hundreds of patients and their caretakers.
                        </p>
                    </div>
                </div>
            </div>
            <div class="row align-items-middle">
                <div class="col-12">
                    <h4 class="text-center">Please first discover self-care techniques using the tool below and then take an online questionnaire to help develop the tool to better serve the community:</h4>
                    <a aria-label="Link to the questionnaire" id="questionnaireLink" target="_blank" href="https://docs.google.com/forms/d/e/1FAIpQLSedMKo6RcrMp04rQ9sUweK46H4jVR4W1E4Aalg7VR8GTJsddg/viewform?usp=pp_url&entry.776715840=<?php echo $uid ?>">
                        <h4 class="mt-1 text-center">Answer the questionnaire!</h4>
                    </a>
                </div>
            </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            <div class="col-lg-12">
            <!--<h2 class="text-center mb-5">PD Care Box</h2>-->
                <div id="criteriasliders" class="d-flex row d-none">
                    <div id="criteria" class="col-md-6">
                        <?php /*printCriteriaSliders($pid);*/
                            /* printCriteriaSliders function inline to change specific criterion detail */
                            $htmlstr = "";
                            $qpid = $db -> quote($pid);
                            $result = $db -> select("SELECT criterion_id, criterion_title, criterion_details, low_title, high_title, low_val, high_val FROM criteria WHERE problem_id = $qpid AND status = 1");
                            if($result === false) {
                                error_log($db -> error());
                                die();
                            } else {
                                foreach ($result as $r) {
                                    if($r["criterion_id"] == 109) {
                                        $details = "How familiar is this technique for the PD community?";
                                    } else {
                                        $details = $r["criterion_details"];
                                    }

                                    $title = $r["criterion_title"];
                                    $id = $r["criterion_id"];
                                    $lt = $r["low_title"];
                                    $ht = $r["high_title"];
                                    $low_val=$r["low_val"];
                                    $high_val=$r["high_val"];
                            
                                    $htmlstr = $htmlstr . "<div class='row my-4'><div class='col-12'><h5>$title: <span data-id='$id' class='badge badge-pill badge-warning criterionvalue'>Move slider to apply</span><br/><small>$details</small></h5><input type='range' min='$low_val' max='$high_val' width='100%' value='-1' class='sliders my-0' data-id='$id'></input><div class='mt-n1'><span class='float-left text-muted'>$lt</span><span class='float-right text-muted'>$ht</span></div></div></div>";  
                                        
                                        
                                }
                            }
                            if ($htmlstr == "") {
                                echo "<p class='lead'>¯\_(ツ)_/¯ this problem has no criteria defined.</p>";
                            } else {
                                echo $htmlstr;
                            }
                        ?>
                            <button aria-label="Discover best matches button" id='getSupportButton' class='btn btn-lg btn-block mt-3'>Discover best matches</button>
                            <button aria-label="Reset Sliders Button" id='clearButton' class='btn btn-block mt-2 mb-3'>Reset sliders</button>
                    </div>
                    <div class="col-md-6 my-4" id="recsHost"> </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5 bg-white">
        <div class="container">
            <hr>
            <p data-toggle="collapse" class="m-0 text-center text-black collapse show">This research is an international collaboration led by University of Oulu, 
            with collaborators from Aalborg University, Fraunhofer University, the University of Pittsburgh, the University of Glasgow, 
            the University of Lisbon and the University of Melbourne.</p>
            <p class="m-0 text-center text-black"><a class="text-center" href="disclaimer.html">Data protection disclaimer.</a></p>
            <img aria-label="Logos of Collaborators" class="img-fluid" src="PD_study_logos_crop.png"/>
            <p data-toggle="collapse" class="m-0 text-center text-black collapse show">We appreciate the help of Parkinson's UK, Davis Phinney Foundation, 
            Parkinson Association of the Rockies, ParkinsonsDisease.net, Parkinson Society British Columbia, 
            Parkinson Society Newfoundland & Labrador, Finnish Parkinson Association, Parkinson's Resource Organization, 
            European Parkinson's Disease Association (EPDA), The Cure Parkinson’s Trust, 
            and Parkinson Wellness Project for spreading the word.</p>
        </div>
    </footer>
    
    <!--Modal Popup-->
    <div id="questModal" class="modal" tabindex="-1" role="dialog" aria-labelledby="Questionnaire" aria-describedby="A reminder to answer the questionnaire.">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Questionnaire</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p style="margin:0px;">Please, remember to answer the provided questionnaire!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dismissBtn" data-dismiss="modal">Later</button>
                    <a aria-label="Button to access the questionnaire" class="btn successBtn" href="https://docs.google.com/forms/d/e/1FAIpQLSedMKo6RcrMp04rQ9sUweK46H4jVR4W1E4Aalg7VR8GTJsddg/viewform?usp=pp_url&entry.776715840=<?php echo $uid ?>">Take the questionnaire now</a>
                </div>
            </div>
        </div>
    </div>

    <!--scripts loaded here-->
    <!--scripts loaded here-->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.4/umd/popper.min.js"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.8.5/js/mdb.min.js"></script>        
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.3.2/bootbox.min.js"></script>
    <script src="./assets/js/scripts.js"></script>    
    <script>
        var problemId = '<?php echo $pid; ?>';
        var userId = "<?php echo $uid; ?>";
        var questionnaireCounter = 0;

        $(function () {
            $.each($(".sliders"), function (index, slider) {
                    
                $(slider).on("input change", function() {
                    var value = slider.value;
                    var badge = $(".criterionvalue[data-id='" + $(slider).data("id") + "']");
                    var val = Math.round(value);
                    badge.html(val);
                    if (badge.hasClass("badge-warning")) {
                        badge.removeClass("badge-warning").addClass("badge-primary");
                    }
                    //we need these when submitting
                    $(slider).attr("data-isused", "true");
                    $(slider).attr("data-currentValue", val);
                });
            });
            $("#getSupportButton").on("click", function f() {
                getSupport();

            });
            $("#clearButton").on("click", function f() {
                resetCriteria();
                document.getElementById('criteriasliders').scrollIntoView({behavior:"smooth", block:"start"});
            });

        });

        function getSupport() {
            var sliders = $(".sliders[data-isused='true']");
            if (sliders.length > 0) {
                var ratingsArray = [[]];
                sliders.each(function () {
                    var ratedValue = this.value;
                    var critId = $(this).data("id");
                    var rating = [critId, ratedValue]
                    ratingsArray.push(rating);
                });
                ratingsArray.shift(); //kill the first empty elem.. push makes a new elem always
                var jsonEncoded = JSON.stringify(ratingsArray, null, 2);
                var postURL = "getrecommendations_pd.php";
                $.post(postURL, {
                    criteria_importances: jsonEncoded
                    , problem_id: problemId
                    , user_id: userId
                    , num_of_options: 10
                    , meta: createMeta()
                }).done(function (data) {
                    recs = $.parseJSON(data);
                    displayRecommendations(recs);
                    var elems = document.querySelectorAll(".getRatingsButton");
                    for (var i=0, len=elems.length; i < len; i++) elems[i].addEventListener("click", getRatingTexts);
                });
                questionnairePopup();
                document.getElementById('recsHost').scrollIntoView({behavior:"smooth"});
            }
            else {
                toastr.options = {
                    positionClass: "toast-top-left",
                    preventDuplicates: true,
                    showDuration: "1000",
                    hideDuration: "1000",
                    closeButton: true
                }
                toastr.warning('Please provide at least one criterion value!');
            }
        }

        function getRatingTexts(event) {

            var postURL = "getrating_text.php";
            var option_id = event.target.value;

            if (event.target.innerHTML == "Hide what this is good for") {
                $("#" + option_id + "Host").empty();
                event.target.innerHTML = "Show what this is good for";
                return
            }

            $("#" + option_id + "Host").empty();
            $.post(postURL, {
                option_id: option_id
            }).done(function (data) {
                ratings = $.parseJSON(data);
                $.each(ratings, function () {
                    var demoStr = "";

                    if(this['info'] !== false) {
                        if(this['havePD'] == "PD_caretaker") {
                        demoStr = " <br> - Caregiver of " + this['birthyear'] + " year old, " + this['yearsSinceDiagnosis'] + " years with PD";
                        } else {
                        demoStr = " <br> - " + this['birthyear'] + " year old, " + this['yearsSinceDiagnosis'] + " years with PD";
                        }
                    }

                    var htmlStr = "<div class='commentBox'><small>\"" + this['text'] + "\"" 
                    + demoStr + "</small></div>";
                    $("#" + option_id + "Host").append(htmlStr);
                });
            })

            if (event.target.innerHTML == "Show what this is good for") {
                event.target.innerHTML = "Hide what this is good for";
            }
        }

        function displayRecommendations(recs) {
            $("#recsHost").empty();
            if (recs.length == 0) {
                toastr.warning('Not enough data in knowledge base!');
                return;
            }
            $.each(recs, function () {
                var title = this.option_title;
                var details = this.option_details;
                var distance = this.avg_distance;
                var practiced_home = "";
                
                /* If the recommendation can be performed home, add a note */
                /*  if (this.practiced_home == true) {
                        practiced_home = "<h5><small><i class='bi bi-house-fill'></i> This can be practiced at home!</h5></small>";
                    } */
                
                title = nl2br(title);
                details = nl2br(details);
                var link = "";
                if(this.primary_link != ""){
                    link = " | <a id='primaryLink' target='_blank' href='" + this.primary_link + 
                    "'> <small>  Learn more </small> </a>";
                }
                var htmlStr = "<div class='col-12'><div class='row'><h5>" + title + link +  "</br><small>" + details 
                + "</small></h5></div><div class='row'><h5><small>"
                + recommendation_distance(distance)
                + "</h5></small></div>"
                /* Can be practiced at home icon and text */
                /*+ "<div class='row practicedHome'>" + practiced_home + "</div>" +*/
                + "<div class='row'>"
                + "<button value='" + this.option_id
                + "' class='btn getRatingsButton mb-2' aria-label='Show what this is good for button'>Show what this is good for</button>"
                + "</div><h5><div id='" + this.option_id + "Host' class='commentDiv row'></div></h5></div><hr>";
                $("#recsHost").append(htmlStr);
            });
        }

        function questionnairePopup() {
            if (questionnaireCounter >= 3) {
                $("#questModal").modal();
                questionnaireCounter = 0;
            } else {
                questionnaireCounter++;
            }
        }

        function recommendation_distance(distance) {
            if (distance <= 33) {
                return "<span class='closeMatch'></span> CLOSE MATCH"
            } else if (distance > 33 && distance <= 66) {
                return "<span class='modMatch'></span> MODERATE MATCH"
            } else {
                return "<span class='poorMatch'></span> POOR MATCH"
            }
        }
            
        function resetCriteria() {
            $("#recsHost").empty();
            var sliders = $(".sliders");
            sliders.each(function () {
                this.value = 0;
                $(this).attr("data-isused", "false");
                    
                var badge = $(".criterionvalue[data-id='" + $(this).data("id") + "']");
                if (badge.hasClass("badge-primary")) {
                    badge.removeClass("badge-primary").addClass("badge-warning");
                    badge.html("Move slider to apply");
                }

            });

        }

        function createMeta() {
            var metaData = {};
            metaData["study"] = "NA";
                
            var extras = <?php echo json_encode($metaItems); ?>;
            extras = Object.entries(extras);
            extras.forEach(function(entry) {
                metaData[entry[0]] = entry[1];
            });

            return JSON.stringify(metaData, null, 2);
        }

    </script>
</body>

</html>