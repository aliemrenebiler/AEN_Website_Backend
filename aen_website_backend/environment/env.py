from dotenv import load_dotenv
from os import getenv


def set_vars():
    load_dotenv("environment/.env")
    ENV = getenv("UI_HOST")

    if ENV == "development":
        load_dotenv("environment/.env.development")
    elif ENV == "production":
        load_dotenv("environment/.env.production")
    else:
        load_dotenv("environment/.env.development")


def get_var(var_name):
    return getenv(var_name)
