<?php

\ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_PROJECT,
                                    'RunProjectTestProjectCheck',
                                    function (\ImmanentCodeChecker\DataObjectPool $objProject) {

                                      \ImmanentCodeChecker\Error\project('RunProjectTestProjectCheck',
                                                                         'expected run project error',
                                                                         array_keys($objProject->getAll()));

                                    });
