<?php
/*
 * Copyright 2022 Michael Lucas <nasumilu@gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Service;

use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Profile;

class PhotoUploader
{

    public function __construct(private string $targetDirectory,
                                private Packages $packages,
                                private SluggerInterface $slugger) { }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    /**
     * Deletes a user profile photo if exists.
     * @param Profile $profile
     * @return void
     */
    public function unlinkOldPhoto(Profile $profile): void
    {
        if(null === $profile->getPhoto()) {
            return;
        }
        $file = new \SplFileObject($this->getTargetDirectory() . '/' . $profile->getPhoto());
        if($file->isFile()) {
            unlink($file->getRealPath());
        }

    }

    public function getUrl(Profile $profile): string
    {
        return $this->packages->getUrl('uploads/' . ($profile->getPhoto() ?? 'profile_avatar.png'));
    }
}