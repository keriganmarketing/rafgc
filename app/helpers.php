<?php

function create($class, $args = [])
{
    return factory($class)->create($args);
}

function make($class, $args = [])
{
    return factory($class)->make($args);
}
