from dotenv import load_dotenv
from os import getenv


class Environment:
    def set_vars(config_folder):
        load_dotenv(f"{config_folder}/.env")
        ENV = getenv("UI_HOST")

        if ENV == "development":
            load_dotenv(f"{config_folder}/.env.development")
        elif ENV == "production":
            load_dotenv(f"{config_folder}/.env.production")
        else:
            load_dotenv(f"{config_folder}/.env.development")

    def get_var(var_name):
        return getenv(var_name)
