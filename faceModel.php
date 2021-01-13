<?php

function compare($a, $b) {
    $output = [];
    $probability = [];
    exec("docker run -it -d macgyvertechnology/face-comparison-model:2 2>&1", $output);
    $out = preg_replace('/[^0-9a-z]/', '', $output[0]);
    // write images to container
    exec('docker cp ' . $a . ' ' . $out . ':/macgyver/temp/known.jpg 2>&1');
    exec('docker cp ' . $b . ' ' . $out . ':/macgyver/temp/test.jpg 2>&1');
    // Run main file
    exec("docker exec -t " . $out . " /bin/bash -c 'python3 /macgyver/main' 2>&1", $probability);
    // Stop the Container
    exec("docker stop " . $out . " 2>&1");
    // Delete the Container
    exec("docker rm " . $out. " 2>&1");
    return $probability[0];
}
