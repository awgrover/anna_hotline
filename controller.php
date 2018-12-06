<?php

# Supply the following url to twilio as the "start" url
# http://build-a-tech.org/anna/control/start

# path:  /$box # return some twiml for this box
# path:  /$box?keys=nn # return some twiml for that next box
# path:  /$box/something.wav # serve sound

# A Box is a directory
# Short descriptive name is best
# The start box is 'start'

# In a directory:
# twi.ml # a twillio ml we will send
# OR
# something.wav # we will send a twiml for playing it, and serve it
# branch.txt # a text file that looks like
# 1 boxname1
# 2 boxname2
# ...
# Will use 'keys' to "go to" the nextbox

$twi_file = "start/twi.ml";
echo readfile( $twi_file );
?>
