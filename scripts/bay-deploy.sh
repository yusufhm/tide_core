#!/bin/bash

BRANCH=$1
if [ -z $BRANCH ]; then
    BRANCH=$(git symbolic-ref --short -q HEAD)
fi

SHA=$( git ls-remote origin | grep refs/heads/${BRANCH} | cut -f 1)
echo "SHA "
echo $SHA
if [ -z $SHA ]; then
    echo -e "Fetching the remote failed. Please ensure you are connected to the internet and can read from the repository."
else
    PROJECT=$(awk '$1=="project:"{print $2}' .lagoon.yml )

    curl -X POST -d projectName=${PROJECT} -d sha=${SHA} -d branchName=${BRANCH} http://rest2tasks-lagoon-master.lagoon.vicsdp.amazee.io/deploy
fi
