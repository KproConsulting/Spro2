import sys
import os

if sys.version_info >= (3, 6):
    import zipfile
else:
    import zipfile36 as zipfile

class kpZipArchive:

    def __init__(self):
        self.getOption()
        self.zipFiles()

    def usage(self):
        print("Questo programma permette di zippare i file contenuti in una cartella; per eseguirlo lanciare il nome del programma seguito da il nome che si vuole dare allo zip e dal percorso della cartella")
        sys.exit(0)

    def getOption(self):
        if not len( sys.argv[1:] ):
            self.usage()

        self.zip_name = sys.argv[1]
        self.path = sys.argv[2]
        
    def zipFiles(self):
        if( self.zip_name is not None and self.path is not None ):

            lista_file = os.listdir(self.path)

            os.chdir(self.path)

            with zipfile.ZipFile(self.zip_name, mode='w') as zf:
                for file in lista_file:
                    print("Add file " + file)
                    zf.write(file)

kpZipArchive()