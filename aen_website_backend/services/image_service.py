import os
from fastapi import HTTPException

_ACCEPTED_IMAGE_FORMATS = ["jpg", "jpeg", "png"]


class ImageService:
    def validate_image(file_path):
        if not os.path.exists(file_path):
            raise HTTPException(
                status_code=404,
                detail="Not found.",
            )
        if not os.path.isfile(file_path):
            raise HTTPException(
                status_code=422,
                detail="Not a file.",
            )
        if not file_path.rsplit(".", 1)[-1].lower() in _ACCEPTED_IMAGE_FORMATS:
            raise HTTPException(
                status_code=422,
                detail="Unaccepted format. Must be JPEG or PNG.",
            )

    def validate_folder(folder_path):
        if not os.path.exists(folder_path):
            raise HTTPException(
                status_code=404,
                detail="Not found.",
            )
        if not os.path.isdir(folder_path):
            raise HTTPException(
                status_code=422,
                detail="Not a folder.",
            )

    def list_images_with_valid_format(folder_path):
        folder_content = os.listdir(folder_path)
        image_file_names = [
            file_name
            for file_name in folder_content
            if len(file_name.rsplit(".", 1)) > 1
            and file_name.rsplit(".", 1)[-1].lower() in _ACCEPTED_IMAGE_FORMATS
        ]
        return image_file_names
