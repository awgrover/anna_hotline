<?php
# FIXME: whack-log, logging, serve log(s)
# FIXME: set ?ct=n for each play, on 10, hangup

# Supply the following url to twilio as the "start" url
# http://build-a-tech.org/anna/controller.php/start

$base_url = 'http://build-a-tech.org/anna/controller.php';

# path:  /controller.php/$box?keys=nn # return some twiml for that next box
# path:  /controller.php/$box/something.wav # serve sound
# path:  /controller.php/$box/timeout # nothing happened, timeout
# path:  /controller.php/$box # return some twiml for this box

# A Box is a directory
# Short descriptive name is best
# The start box is 'start'
# The 'oops' box should play a sound

# Sound Box with branching
# something.wav # we will send a twiml for playing it, and serve it
# branch.txt # a text file that looks like
# 1 boxname1
# 2 boxname2
# ...
# Will use 'keys' to "go to" the nextbox
# FIXME: If no branch.txt, will hangup after

$my_log = fopen("controller_log", "a");

function logit($msg) {
    global $my_log;
    if ($my_log) {
        fwrite($my_log, "" . Date('c') . " " );
        fwrite($my_log, $msg );
        fwrite($my_log, "\n" );
        }
    }

# TWI.ML Box
# twi.ml # a twillio ml we will send


# debug as comment
logit( "REQUEST_URI ".$_SERVER['REQUEST_URI'] );
logit( "PATH_INFO " . $_SERVER['PATH_INFO'] );

# We seem to be able to use $_SERVER['PATH_INFO'] 
# which is the portion after our cgi script name => '/start'


function play_sound($box, $wav_file) {
    # twi.ml for play sound
    global $base_url;
?><Response>
    <Gather action="<?php echo $base_url . "/" . $box; ?>" finishOnKey="" input="dtmf" language="en-US" method="GET" numDigits="1" speechTimeout="auto" timeout="3">
        <Play loop="1"><?php echo $base_url . "/" . $box . "/" . basename($wav_file); ?></Play>
    </Gather>
    <Redirect method="POST"><?php echo $base_url . "/" . $box; ?>/timeout</Redirect>
</Response>
<?php
}

function play_oops($msg) {
    # when we have some error
    echo "<!-- $msg -->\n";
    play_sound( 'oops', 'oops/oops.wav' );
    }

function twi_ml_for_box($box) {
    # no keys=n, so we return something...

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    echo "<!-- path: ".$_SERVER['PATH_INFO']." -->\n";

    $twi_file = $box . "/twi.ml";
    $wav_file = $box . "/*.wav";
    $wav_files = glob( $wav_file );

    # path /controller.php/$box # return the twi.ml if there is one
    if ( file_exists( $twi_file ) ) {
        echo readfile( $twi_file );
        }

    # path /controller.php/$box # play a sound if there is one
    elseif (sizeof( $wav_files > 0 )) {
        $wav_file = $wav_files[0];
        play_sound( $box, $wav_file );
        }

    # Unknown
    else {
        $twi_file = $box . "/twi.ml";
        $wav_file = $wav_files[0];
        echo "<!-- No $twi_file, nor $wav_file -->\n";
        return 0;
        }
    return 1;
    }

$box = ltrim($_SERVER['PATH_INFO'], '/' );
logit("box: ".$box);

# path:  /controller.php/$box?keys=nn # return branch twiml for that next box
if ( $_GET['keys'] ) {
    logit("KEYS ".$_GET['keys']);
    $keys = $_GET['keys'];

    $branch_txt = $box . "/branch.txt";
    if (file_exists( $branch_txt )) {
        logit("Test key=$keys in '/$branch_txt'");
        foreach(file($branch_txt) as $key_box) {
            # a branch.txt file has lines:
            # nn boxname
            # We skip anything that doesn't start with digits, so comment away!
            if ( preg_match('/^([0-9]+)\s(.+)/', $key_box, $parts) ) {
                # found it?
                logit("br: ".$parts[1]." -> ".$parts[2]);
                if ($parts[1] == $keys) {
                    logit("br!! ".$parts[1]." -> ".$parts[2]);
                    twi_ml_for_box( $parts[2] );
                    exit; # done
                    }
                }
            }
        # didn't match a key, do this box again
        logit("key=$keys didn't match in '$branch_txt', so again!");
        twi_ml_for_box($box);
        }
    else {
        play_oops("keys=$keys for '/$box', but no '$branch_txt'");
        }
    }

# path /controller.php/$box # return the twi.ml if there is one
# path:  /controller.php/$box/something.wav # serve sound
elseif( twi_ml_for_box($box) ) {
    # did it, or failed
    }
?>
