<?php

//This application developed by www.webinfopedia.com
//visit www.webinfopedia.com for PHP,mysql,SEO,HTML5 and designing related articles
//check for file
if(!empty($_FILES)){
					//Image Storage Directory
					$img_dir="images/";
					$img = explode('.', $_FILES['thumb_img']['name']);
					//Original File
					$originalImage=$img_dir.'Original_'.$_FILES['thumb_img']['name'];
					//Thumbnail file name File
					$image_filePath=$_FILES['thumb_img']['tmp_name'];
					$img_fileName=$img[0].time().'_Thumb.'.$img[1];
					$img_thumb = $img_dir . $img_fileName;
					$extension = strtolower($img[1]);
					
						//Check the file format before upload
						if(in_array($extension , array('jpg','jpeg', 'gif', 'png', 'bmp'))){
						
						//Find the height and width of the image
						list($gotwidth, $gotheight, $gottype, $gotattr)= getimagesize($image_filePath); 	
						
						//---------- To create thumbnail of image---------------
						if($extension=="jpg" || $extension=="jpeg" ){
						$src = imagecreatefromjpeg($_FILES['thumb_img']['tmp_name']);
						}
						else if($extension=="png"){
						$src = imagecreatefrompng($_FILES['thumb_img']['tmp_name']);
						}
						else{
						$src = imagecreatefromgif($_FILES['thumb_img']['tmp_name']);
						}
						list($width,$height)=getimagesize($_FILES['thumb_img']['tmp_name']);
						
						//This application developed by www.webinfopedia.com
						//Check the image is small that 124px
						if($gotwidth>=124)
						{
							//if bigger set it to 124
						$newwidth=124;
						}else
						{
							//if small let it be original
						$newwidth=$gotwidth;
						}
						//Find the new height
						$newheight=round(($gotheight*$newwidth)/$gotwidth);
						//Creating thumbnail
						$tmp=imagecreatetruecolor($newwidth,$newheight);
						imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight, $width,$height);
						//Create thumbnail image
						$createImageSave=imagejpeg($tmp,$img_thumb,100);
						
								if($createImageSave)
								{
									//upload the original file
								$uploadOrginal=move_uploaded_file($_FILES['thumb_img']['tmp_name'],$originalImage);	
								if($uploadOrginal)
								{
									//if successfull
								header("Location:index.php?thumb=".base64_encode($img_fileName)."&original=".base64_encode($originalImage)."");	
								}
								}
								
                        }
                        else{
							//If file formet not supported show error
						header("Location:index.php?message=error&fileType=false");	
						?>
						<?php
                        }
                }
?>