from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from utils.environment import Environment
from routers import images

Environment.set_vars("config")

allowed_origins = [
    Environment.get_var("UI_HOST"),
]

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=allowed_origins,
    allow_credentials=True,
    allow_methods=["GET"],
    allow_headers=["*"],
)

app.include_router(images.router)
