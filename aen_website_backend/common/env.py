from dotenv import load_dotenv
from os import getenv


def set_env_vars():
    load_dotenv("env/.env")
    ENV = getenv("UI_HOST")

    if ENV == "development":
        load_dotenv("env/.env.development")
    elif ENV == "production":
        load_dotenv("env/.env.production")
    else:
        load_dotenv("env/.env.development")


def get_env_var(var_name):
    return getenv(var_name)
