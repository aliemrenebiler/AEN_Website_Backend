import os
from fastapi import APIRouter
from fastapi.responses import FileResponse
from services.image_service import ImageService
from utils.environment import get_environment_variables

images_router = APIRouter(prefix="/images")

env_vars = get_environment_variables()
_image_folder_path = env_vars.image_folder_path


@images_router.get("/{folder_name}/{file_name}")
async def get_image_from_folder(folder_name: str, file_name: str):
    file_path = os.path.join(_image_folder_path, folder_name, file_name)

    ImageService.validate_image(file_path)

    return FileResponse(file_path)


@images_router.get("/{folder_name}")
async def get_image_names_in_folder(folder_name: str):
    print(_image_folder_path)
    folder_path = os.path.join(_image_folder_path, folder_name)

    ImageService.validate_folder(folder_path)

    return ImageService.list_images_with_valid_format(folder_path)
