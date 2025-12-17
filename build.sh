#!/bin/bash
find ./helloassovars -type f|grep -v "/.DS_Store"| zip helloassovars-1.0.0.zip -@