<?php

\ImmanentChecker\Check\register(\ImmanentChecker\STAGE_PROJECT,
                                    'RunProjectTestProjectCheck',
                                    function (\ImmanentChecker\DataObjectPool $objProject) {

                                      \ImmanentChecker\Error\project('RunProjectTestProjectCheck',
                                                                         'expected run project error',
                                                                         array_keys($objProject->getAll()));

                                    });
