from genericpath import isdir
from fastapi import FastAPI, HTTPException
from fastapi.responses import FileResponse
from const import *
import os

app = FastAPI()

ACCEPTED_IMAGE_FORMATS = ["jpg", "jpeg", "png"]


@app.get("/images/{folder_name}/{file_name}")
async def get_image_from_folder(folder_name: str, file_name: str):
    file_path = os.path.join(IMAGE_FOLDER_PATH, folder_name, file_name)

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
    if not file_path.rsplit(".", 1)[-1].lower() in ACCEPTED_IMAGE_FORMATS:
        raise HTTPException(
            status_code=422,
            detail="Unaccepted format. Must be JPEG or PNG.",
        )

    return FileResponse(file_path)


@app.get("/images/{folder_name}")
async def get_image_names_in_folder(folder_name: str):
    folder_path = os.path.join(IMAGE_FOLDER_PATH, folder_name)

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

    folder_content = os.listdir(folder_path)
    return [
        file_name
        for file_name in folder_content
        if len(file_name.rsplit(".", 1)) > 1
        and file_name.rsplit(".", 1)[-1].lower() in ACCEPTED_IMAGE_FORMATS
    ]
