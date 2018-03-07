#!/bin/sh
##
# Post comment with deployment URL to JIRA.
#

JIRA_URL=$1
USER=$2
PASSWORD=$3
BRANCH=$4
PREFIX=$5
PR=$6

extract_issue() {
  local branch=$(echo $1 | tr '[:upper:]' '[:lower:]')
  local prefix=$2
  echo $branch|sed -n "s/feature\/\($prefix-[0-9]\{1,\}\).*/\1/p"
}

generate_data() {
  cat <<EOF
  {
    "body": "$COMMENT"
  }
EOF
}

ISSUE=$(extract_issue "$BRANCH" $PREFIX)
[ "$ISSUE" == "" ] && echo "Branch does not contain issue number" && exit 0

COMMENT="Deployed to http://nginx-php-vicgovau-$PR.lagoon.vicsdp.amazee.io"

echo "Posting comment \"$COMMENT\" for issue \"$ISSUE\""

DATA="{'body': $COMMENT}"
curl -s -u $USER:$PASSWORD -X POST --data "$(generate_data)" -H "Content-type: application/json" "$JIRA_URL/rest/api/2/issue/$ISSUE/comment"
