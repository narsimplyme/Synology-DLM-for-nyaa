#get name from INFO
name=$(shell grep -w name INFO | cut -d ":" -f 2 | sed -e 's/[",\ ]*//g' )
#get version from INFO
version=$(shell grep -w version INFO | cut -d ":" -f 2 | sed -e 's/[",\ ]*//g' )

#packe name
dlm=${name}-${version}.dlm

all: ${dlm}

${dlm}: INFO search.php
	tar zcf ${dlm} $?

clean:
	rm *.dlm
