<Response>
    <Gather action="http://build-a-tech.org/control/start" finishOnKey="" input="dtmf" language="en-US" method="POST" numDigits="1" speechTimeout="auto" timeout="3">
        <Play loop="1">http://build-a-tech.org/control/start/homesickintro_edited.wav</Play>
    </Gather>
    <Redirect method="POST">http://build-a-tech.org/control/start/timeout</Redirect>
</Response>
