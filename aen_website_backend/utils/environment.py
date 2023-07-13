from typing import List
from pydantic_settings import BaseSettings
import os


def get_environment():
    return os.environ["ENV"].lower() if "ENV" in os.environ else "development"


def set_environment_file_path():
    environment = get_environment()
    return f"config/.env.{environment}"


class Settings(BaseSettings):
    ui_host: str
    image_folder_path: str
    accepted_image_formats: List[str]


def get_environment_variables():
    env_file_path = set_environment_file_path()
    return Settings(_env_file=env_file_path)
