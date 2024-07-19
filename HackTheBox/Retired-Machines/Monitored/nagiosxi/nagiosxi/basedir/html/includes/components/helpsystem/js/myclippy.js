clippy.load('Merlin', function(agent) {
    agent.show();
    agent.moveTo(100,100);
    agent.speak('Clippy is a punk, lets get magical, I am Merlin');
    for (i = 0; i < 5; i++) { 
        agent.animate();
    }
    agent.play('Announce');
    agent.speak('Hear ye hear ye, I have found the best monitoring software in the land! Nagios XI! Now, lets get started and get things done!');
    for (i = 0; i < 15; i++) { 
        agent.animate();
    }
});