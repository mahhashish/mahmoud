/*
 * See ../license.txt for licensing
 *
 * File Name: ru.js
 * 	Romanian language file.
 *
 * File Authors:
 * 	
 */

kfm.lang=
{
Dir:
	"ltr", // language direction
ErrorPrefix:
	"eroare: ",
// what you see on the main page
Directories:
	"Mapele",
CurrentWorkingDir:
	"Mapă curenta: \"%1\"",
Logs:
	"Log-uri",
FileUpload:
	"Încărcarea fişierului",
DirEmpty:
	"fişierele n-au fost găsite în \"%1\"",

// right click menu item directory
// directory
CreateSubDir:
	"crearea submapei",
DeleteDir:
	"ştergerea mapei",
RenameDir:
	"schimbarea numelui mapei",

//file
"delete file":
	"ştergerea fişierului",
"rename file": "schimbarea numelui fişierului",
"rotate clockwise":
	"rotaţie la dreaptă",
"rotate anti-clockwise":
	"rotaţie la stângă",
"resize image":
	"schimbarea mărimii imaginii",
"change caption":
	"schimbarea titlului",

// create a file
WhatFilenameToCreateAs:
	"Fişierul va fi salvat ca?",
AskIfOverwrite:
	"Fişierul \"%1\" există. Rescrierea?",
NoForwardslash:
	"\nEste ilegal de utilizat '/' în numele fişierului",

// messages management
CreateDirMessage:
	"Crearea submapei în \"%1\":",
DelDirMessage:
	"De şters mapă \"%1\"?",
DelFileMessage:
	"De şters fişierul \"%1\"",
DelMultipleFilesMessage:
	"De şters fişierele selectate?\n\n'",
DownloadFileFromMessage:
	"De unde se va încărca fişierul?",
FileSavedAsMessage:
	"Cum va fi salvat fişierul?",

//resize file
CurrentSize:
	"Mărimea curentă: \"%1\" x \"%2\"\n",
NewWidth:
	"Laţime nouă?",
NewWidthConfirmTxt:
	"Laţime nouă: \"%1\"\n",
NewHeight:
	"Înalţime nouă?",
NewHeightConfirmTxt:
	"Înălţime nouă: \"%1\"\n\nIs this correct?",

// log messages
RenamedFile:
	"schimbarea numelui fişierului din \"%1\" în \"%2\".",
DirRefreshed:
	"mapele au fost reînnoite.",
FilesRefreshed:
	"fişierele au fost reînnoite.",
NotMoreThanOneFile:
	"eroare: Dvs. nu puteţi să selectaţi decât un singur fişier odată",
UnknownPanelState:
	"eroare: starea panelului necunoscută.",
//MissingDirWrapper:
//	"error: missing directory wrapper: \"kfm_directories%1\".",
SetStylesError:
	"eroare: nu se poate de instalat \"%1\" în \"%2\.",
NoPanel:
	"eroare: panela \"%1\" nu există.",
FileSelected:
	"fişierul ales: \"%1\"",
Log_ChangeCaption:
	"schimbarea titlului din \"%1\" în \"%2\"",
UrlNotValidLog:
	"Eroare: URL trebui să înceapă cu \"http:\"",
MovingFilesTo:
	"mutarea fişierului din [\"%1\"] în \"%2\"",

// error messages
DirectoryNameExists:
	"mapă cu această nume deja există.",
FileNameNotAllowd:
	"eroare: numele fişierului este blocată",
CouldNotWriteFile:
	"eroare: este imposibil de salvat fişierul \"%1\".",
CouldNotRemoveDir:
	"este imposibil de şters mapă.\npoate conţine fişiere activi",
UrlNotValid:
	"eroare: URL trebuie să înceapă cu \"http:\"",
CouldNotDownloadFile:
	"eroare: este imposibil de încărcat fişierul \"%1\".",
FileTooLargeForThumb:
	"eroare: \"%1\" este prea mare pentru crearea schiţei. Schimbaţi fişierul cu un alt cu o mărime mai mică",
CouldntReadDir:
	"eroare: este imposibil de citit mapă",
FilenameAlreadyExists:
	"eroare: fişierul cu acelaşi nume deja există",

// new in 0.5
EditTextFile:
	"redactarea fişierului text",
CloseWithoutSavingQuestion:
	"OK pentru închiderea fără salvarea?",
CloseWithoutSaving:
	"Închiderea fără salvarea",
SaveThenClose:
	"Închide cu salvarea",
SaveThenCloseQuestion:
	"Salva schimbările?",

// new in 0.6
LockPanels:
	"fixarea panelelor",
UnlockPanels:
	"scoaterea fixării panelelor",
"create empty file":
	"crearea fişierului gol",
DownloadFileFromUrl:
	"încărca din URL",
DirectoryProperties:
	"Caracteristicile mapei",
"select all":
	"sublinia tot",
SelectNone:
	"anula subliniare",
InvertSelection:
	"invertirea sublinierii",
LoadingKFM:
	"încărcare KFM",
Name:
	"nume",
"file details":
	"Informaţie despre fişier",
Search:
	"Căutare",
IllegalDirectoryName:
	"numele mapei este incorectă \"%1\"",
RecursiveDeleteWarning:
	"\"%1\" contine fişiere!\nOK pentru ştergerea mapei şi tuturor fişierelor din ea?\n*ATENŢIE* PROCESUL ESTE INREVERSIBIL!",
RmdirFailed:
	"imposibil de şters mapă \"%1\"",
DirNotInDb:
	"mapă lipseşte în baza de date",
ShowPanel:
	"de vizualizat panelă \"%1\"",
"change caption":
	"Schimbarea Titlului",
NewDirectory:
	"Mapă nouă",
Upload:
	"Încărcare",
NewCaptionIsThisCorrect:
	"Titlu nou:\n%1\n\nEste Corect?",
Close:
	"închide",
Loading:
	"se încarcă",
AreYouSureYouWantToCloseKFM:
	"OK pentru închide fereastră cu KFM?",
PleaseSelectFileBeforeRename:
	"Alegeţi un fişier pentru schimbarea numelui",
RenameOnlyOneFile:
	"Puteţi să schimbaţi numele a unui fişier odată",
RenameFileToWhat:
	"De schimba numele fişierului \"%1\" în ...?",
NoRestrictions:
	"nu-s restricţii",
Filename:
	"numele fişierului",
Maximise:
	"extinderea",
Minimise:
	"reducerea",
AllowedFileExtensions:
	"extensiile acceptabile a fişierului",
Filesize:
	"mărimea fişierului",
MoveDown:
	"neglijarea",
Mimetype:
	"tipul mime",
MoveUp:
	"ridicarea",
Restore:
	"restituirea",
Caption:
	"titlu",
CopyFromURL:
	"A copia din URL",
ExtractZippedFile:
	"UNZIP",


// new in 0.8
"view image":
	"vizionarea imaginii",
"return thumbnail to opener":
	"utilizarea schiţei în loc de imagine încărcată",
"add tags to files":
	"adăuga tag-uri la fişier(re)",
"remove tags from files":
	"scoate tag-uri din fişier(re)",
HowWouldYouLikeToRenameTheseFiles:
	"Cum doriţi să schimbaţi numele acestor fişierelor?\n\nde exemplu: \"images-***.jpg\" va schimba numele în \"images-001.jpg\", \"images-002.jpg\", ...",
YouMustPlaceTheWildcard:
	"Dvs. trebuie să puneţi un semn de grupă (wildcard character) * undeva în şablonul numelui de fişier",
YouNeedMoreThan:
	"Dvs. trebuie să puneţi mai mult decât %1 * caractere pentru a crea %2 numele de fişieri",
NoFilesSelected:
	"nici un fişer a fost selectat",
Tags:
	"tag-uri",
IfYouUseMultipleWildcards:
	"Dacă Dvs. utilizaţi mai multe semne de grupă (wildcards) în şablonul numelui de fişier, ele trebuie să fie grupate împreună",
NewCaption:
	"Nou Titlu",
WhatMaximumSize:
	"Care mărimea maximă trebuie să fie returnată?",
CommaSeparated:
	"împărţite de virgulă",
WhatIsTheNewTag:
	"Care este tag-ul nou?\nPentru nişte tag-uri, utilizaţi virgulă pentru împarţire.",
WhichTagsDoYouWantToRemove:
	"Care tag-uri trebuie să fie scoase?\nPentru nişte tag-uri, utilizaţi virgulă pentru împarţire."

,
// New in 0.9
AllFiles: "toate fişieri",
AndNMore: "...şi %1 încă...",
Browse: "Browse...",
ExtractAfterUpload: "extragerea după primirea",
NotAnImageOrImageDimensionsNotReported: "eroare: nu este o imagine, sau dimensiunile imaginii nu au fost stabilite",
RenameTheDirectoryToWhat: "Modifica numele mapei din '%1' în...?",
RenamedDirectoryAs: "Numele mapei a fost modificată din '%1' în '%2'",
TheFilenameShouldEndWithN: "La sfârşitul numii de fişier trebuie să puneţi %1",
WhatFilenameDoYouWantToUse: "Care numele de fişier doriţi să utilizaţi?"

,
// New in 1.0
ZipUpFiles: "adăuga fişieri în arhivă",
Cancel: "cancel"
	, // new in 1.2
	Icons                   : "iconiţe", // used to select mode of file view
	ListView                : "lista", // used to select mode of file view
	SendToCms               : "trimite la CMS", // close KFM and return the selected files to the CMS
	CannotMoveDirectory     : "acces interzis: imposibil de mutat mapă",
	LastModified            : "ultima dată schimbării", // part of File Details
	ImageDimensions         : "dimensiunile imaginii", // part of File Details
	CouldNotMoveFiles       : "eroare: imposibil de mutat fişier[e]",
	CopyFiles               : "copia fişierele", // when dragging files to a directory, two choices appear - "copy files" and "move files"
	MoveFiles               : "muta fişierele",
	"about KFM"                : "despre KFM",
	Errors                  : "Eroare",
	Ok                      : "OK" // as in "OK / Cancel"
}
