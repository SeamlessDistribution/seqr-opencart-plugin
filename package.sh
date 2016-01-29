#!/bin/bash
LAST_TAG=$(git tag | tail -1)
if [ LAST_TAG ]; then
	rm -rf target 2> /dev/null
	rm -rf *.zip 2> /dev/null
	mkdir -p target/upload
	cp -Rf admin catalog target/upload/
	cp seqr_install.ocmod.xml target/install.xml
	cd target
	zip -qr "seqr-$LAST_TAG.ocmod.zip" upload
	zip -qr "seqr-$LAST_TAG.ocmod.zip" install.xml
	cd ../
else	
	echo 'No tag was found. Create tag using git tag command'
fi
