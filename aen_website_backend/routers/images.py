import os
from fastapi import APIRouter
from fastapi.responses import FileResponse
from services.image_service import ImageService
from utils.environment import Environment

router = APIRouter(prefix="/images")

Environment.set_vars("config")

image_folder_path = Environment.get_var("IMAGE_FOLDER_PATH")


@router.get("/{folder_name}/{file_name}")
async def get_image_from_folder(folder_name: str, file_name: str):
    file_path = os.path.join(image_folder_path, folder_name, file_name)

    ImageService.validate_image(file_path)

    return FileResponse(file_path)


@router.get("/{folder_name}")
async def get_image_names_in_folder(folder_name: str):
    print(image_folder_path)
    folder_path = os.path.join(image_folder_path, folder_name)

    ImageService.validate_folder(folder_path)

    return ImageService.list_images_with_valid_format(folder_path)
