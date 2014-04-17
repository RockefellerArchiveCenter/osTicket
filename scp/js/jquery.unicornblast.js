$(document).ready(function () {
    
    //defaults in da house
    var settings = {
        start: 'konamiCode',
        numberOfFlyBys: 1,
    }
    
    // If options exist, lets merge them with our default settings
    var options = $.extend(settings, options);
    
    var animationRunning = false;
    var audioSupported = false;
    var content = '<img id="bigRainbow" style="display: none" src="img/rainbow.gif" />';
    content += '<img id="flyingUnicorn0" class="flyingUnicorn" style="display: none" src="img/flyingUnicorn0.gif" />';
    content += '<img id="flyingUnicorn1" class="flyingUnicorn" style="display: none" src="img/flyingUnicorn1.gif" />';
    content += '<img id="flyingUnicorn2" class="flyingUnicorn" style="display: none" src="img/flyingUnicorn2.gif" />';
    content += '<img id="flyingUnicorn3" class="flyingUnicorn" style="display: none" src="img/flyingUnicorn3.gif" />';
    
    //Add rainbow and unicorns to page only if they do not already exist
    if ($('#bigRainbow').size() == 0) {
        $('body').append(content);
    }
    
    var number = Math.floor(Math.random() * 50);
    console.log(number)
    
    //Start logic 
    if ($('.alert-info').length && number === 1) {
        if (animationRunning == false) {
            start();
            }
    } else if (options.start == 'konamiCode') {
        var keysPressed =[];
        konamiCode = "38,38,40,40,37,39,37,39,66,65";
        
        $(window).bind('keydown', function (e) {
            if (animationRunning == false) {
                keysPressed.push(e.keyCode);
                
                //if size > 11, trim to 10 most recent key entries
                if (keysPressed.length > 10) {
                    //remove first
                    keysPressed.splice(0, 1);
                }
                
                if (keysPressed.toString().indexOf(konamiCode) >= 0) {
                    if (audioSupported) {
                        document.getElementById('contraSound').play();
                    }
                    start();
                }
            }
        });
    }
    
    //Show unicorns
    var rainbow;
    var rHeight;
    var windowWidth;
    var windowHeight
    var flyByCount = 0;
    var entrySideCount = 0;
    var entrySide =[ 'left', 'top', 'right', 'bottom'];
    
    function start() {
        animationRunning = true;
        flyByCount = 0;
        windowWidth = $(window).width();
        windowHeight = $(window).height();
        
        //Set rainbow size and css as window size may have changed
        rainbow = $("#bigRainbow").attr('width', windowWidth / 1.2);
        rHeight = rainbow.height();
        var rWidth = rainbow.width();
        
        rainbow.css({
            "position": "fixed",
            "bottom": "-" + rHeight + "px",
            "left": (windowWidth / 2) - (rWidth / 2),
            "display": "block",
            opacity: 0.0
        })
        
        
        //Raise the rainbow!!!
        rainbow.animate({
            bottom: "0px",
            opacity: 1.0
        },
        1800, function () {
            // Rainbow raise complete. Summon the unicorns!!!
            flyUnicorn();
        });
    }
    
    function flyUnicorn() {
        var entryPoint;
        var exitPoint;
        var unicornId = 'flyingUnicorn' + Math.floor(Math.random() * 4);
        var unicornImg = $("#" + unicornId);
        
        if (entrySide[entrySideCount] == 'left' || entrySide[entrySideCount] == 'right') {
            entryPoint = Math.floor(Math.random() * windowHeight);
            exitPoint = windowHeight - entryPoint;
        } else {
            entryPoint = Math.floor(Math.random() * windowWidth);
            exitPoint = windowWidth - entryPoint;
        }
        
        if (entrySide[entrySideCount] == 'left') {
            unicornImg.css({
                "position": "fixed",
                "top": entryPoint + "px",
                "left": "-" + unicornImg.width() + "px",
                "display": "block"
            }).animate({
                "left": windowWidth + "px",
                "top": exitPoint - unicornImg.height() + "px",
            },
            2000, function () {
                checkComplete();
            });
        } else if (entrySide[entrySideCount] == 'right') {
            unicornImg.css({
                "position": "fixed",
                "top": entryPoint + "px",
                "left": windowWidth + "px",
                "display": "block"
            }).animate({
                "left": "-" + unicornImg.width() + "px",
                "top": exitPoint - unicornImg.height() + "px",
            },
            2000, function () {
                checkComplete();
            });
        } else if (entrySide[entrySideCount] == 'top') {
            unicornImg.css({
                "position": "fixed",
                "top": "-" + unicornImg.height() + "px",
                "left": entryPoint + "px",
                "display": "block"
            }).animate({
                "left": exitPoint - unicornImg.width() + "px",
                "top": windowHeight + "px",
            },
            2000, function () {
                checkComplete();
            });
        } else if (entrySide[entrySideCount] == 'bottom') {
            unicornImg.css({
                "position": "fixed",
                "top": windowHeight + "px",
                "left": entryPoint + "px",
                "display": "block"
            }).animate({
                "left": exitPoint - unicornImg.width() + "px",
                "top": "-" + unicornImg.height() + "px",
            },
            2000, function () {
                checkComplete();
            });
        }
        
        entrySideCount++;
        if (entrySideCount == 4) {
            entrySideCount = 0;
        }
        
        //Increment fly by count
        flyByCount++;
    }
    
    
    function checkComplete() {
        if (flyByCount != options.numberOfFlyBys) {
            //Keep flying!!!
            flyUnicorn();
        } else {
            //Hide all the unicors
            $(".flyingUnicorn").hide();
            
            //Hide the rainbow
            rainbow.animate({
                "bottom": "-" + rHeight + "px",
                opacity: 0.0
            },
            2000, function () {
                animationRunning = false;
            });
        }
    }
});