<Response>
    <Gather action="http://build-a-tech.org/controller.php/start" finishOnKey="" input="dtmf" language="en-US" method="POST" numDigits="1" speechTimeout="auto" timeout="3">
        <Play loop="1">http://build-a-tech.org/controller.php/start/homesickintro_edited.wav</Play>
    </Gather>
    <Redirect method="POST">http://build-a-tech.org/controller.php/start/timeout</Redirect>
</Response>
