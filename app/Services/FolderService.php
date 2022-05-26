<?php 

namespace App\Services;

use App\Models\Folder;

class FolderService
{
    public function __construct($folderString){
        $this->folderString = trim($folderString);
    }

    public function getIfNotExistCreateFolder()
    {   
        $parentFolder = null;
        $folderPath = '';
        if($this->folderString=='')
        {
            return [$parentFolder,$folderPath];            
        }
        $pattern = "/[\\\\|\/]/i";
        $parts = preg_split($pattern, $this->folderString);
        $totalFolder = count($parts);
        for ($i=0; $i <$totalFolder ; $i++) {
            $part = trim($parts[$i]);
            if($part=='')continue;
            $folder = Folder::where('title',$part)->where('folder_id',$parentFolder)->first();
            if(!$folder)
            {
                $createdFolder = Folder::create([
                    'title' => $part,
                    'folder_id' => $parentFolder,
                ]);

                $parentFolder = $createdFolder->id;
                $folderPath .= $createdFolder->title.'/';
            }
            else{
                $parentFolder = $folder->id;
                $folderPath .= $folder->title.'/';
            }
        }

        return [$parentFolder,$folderPath];
    }

    public function checkFolderStructureExistance()
    {
        if($this->folderString=='')
        {
            // dump('here');
            return 0;
            
        }

        $pattern = "/[\\\\|\/]/i";
        $parts = preg_split($pattern, $this->folderString);
        $totalFolder = count($parts);
        $parentFolder = null;
        for ($i=0; $i <$totalFolder ; $i++) {
            $folder = Folder::where('title',$parts[$i])->where('folder_id',$parentFolder)->first();
            if(!$folder)
            {
                // dump($parentFolder,$parts[$i]);
                return 0;
            }

            $parentFolder = $folder->id;
        }

        return $folder->id;
    }

    public static function getFolderPathtoParent($folder_id,$folderString)
    {
        $folder = Folder::where('id',$folder_id)->first();
        if(!$folder)return $folderString;
        // dump($folderString);
        $folderString = $folder->title.'/'.$folderString;
        if($folder->folder_id!=null)
        {
            $folderString = self::getFolderPathtoParent($folder->folder_id,$folderString);
        }

        return $folderString;
    }
}
