#! /bin/bash
# Based on scripts by Dean Clatworthy, Brent Shepherd and Patrick Rauland.
# https://github.com/BFTrick/jotform-integration/blob/master/deploy.sh

# This script lives in the git repo and is used to deploy updates to BOTH git 
# and svn at the same time. Use 'bash deploy.sh' instead of 'git push' when 
# updating to the WordPress plugin repo.

# main config
PLUGINSLUG="glift-go-game"
CURRENTDIR=`pwd`
MAINFILE="glift-go-game.php" # this should be the name of your main php file in the wordpress plugin

# git config
GITPATH="$CURRENTDIR/" # this file should be in the base of your git repository

# svn config
SVNPATH="/tmp/$PLUGINSLUG" # path to a temp SVN repo. No trailing slash required and don't add trunk.
SVNURL="http://plugins.svn.wordpress.org/glift-go-game" # Remote SVN repo on wordpress.org, with no trailing slash
SVNUSER="gogameguru" # your svn username


# Let's begin...
echo ".........................................."
echo 
echo "Preparing to deploy WordPress plugin"
echo 
echo ".........................................."
echo 

# Check version in readme.txt is the same as plugin file
NEWVERSION1=`grep "^Stable tag" $GITPATH/readme.txt | awk -F' ' '{print $3}'`
echo "readme version: $NEWVERSION1"
NEWVERSION2=`grep "^Version" $GITPATH/$MAINFILE | awk -F' ' '{print $2}'`
echo "$MAINFILE version: $NEWVERSION2"

if [ "$NEWVERSION1" != "$NEWVERSION2" ]; then echo "Versions don't match. Exiting..."; exit 1; fi

echo "Versions match in readme.txt and $MAINFILE. Let's proceed..."

cd $GITPATH
echo -e "Enter a commit message for this new version: \c"
read COMMITMSG
git commit -am "$COMMITMSG"

echo "Tagging new version in git"
git tag -a "$NEWVERSION1" -m "$COMMITMSG"

echo "Pushing latest commit to origin, with tags"
git push origin master
git push origin master --tags

echo 
echo "Creating local copy of SVN repo..."
svn co $SVNURL $SVNPATH

echo "Exporting the HEAD of master from git to the trunk of SVN"
git checkout-index -a -f --prefix=$SVNPATH/trunk/

echo "Ignoring github specific files & deployment script"
svn propset svn:ignore "deploy.sh
README.md
.git
.gitignore" "$SVNPATH/trunk/"

echo "Changing directory to SVN"
cd $SVNPATH/trunk/
# Add all new files that are not set to be ignored
svn status | grep -v "^.[ \t]*\..*" | grep "^?" | awk '{print $2}' | xargs svn add
echo "Committing to trunk"
svn commit --username=$SVNUSER -m "$COMMITMSG"

echo "Creating new SVN tag & committing it"
cd $SVNPATH
svn copy trunk/ tags/$NEWVERSION1/
cd $SVNPATH/tags/$NEWVERSION1
svn commit --username=$SVNUSER -m "$COMMITMSG"

echo "Removing temporary directory $SVNPATH"
rm -fr $SVNPATH/

echo "*** FIN ***"
