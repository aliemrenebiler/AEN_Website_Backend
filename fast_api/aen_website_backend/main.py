from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from utils.environment import get_environment_variables
from routers.images import images_router

env_vars = get_environment_variables()

allowed_origins = [env_vars.ui_host]

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=allowed_origins,
    allow_credentials=True,
    allow_methods=["GET"],
    allow_headers=["*"],
)

app.include_router(images_router)
