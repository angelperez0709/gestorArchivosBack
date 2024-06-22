# Gestor Archivos Back

This repository contains the back-end code for a file management system. It provides the server-side logic and APIs required for managing files, authentication, and other backend functionalities.

## Table of Contents

- [Features](#features)
- [Technologies Used](#technologies-used)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)

## Features

- **User Authentication**: Secure login and registration.
- **File Management**: Upload, download, and organize files.
- **API Endpoints**: RESTful APIs for interacting with the file management system.

## Technologies Used

- **PHP**: Server-side scripting language.
- **MySQL**: Database management.
- **Apache**: Web server.
- **Composer**: Dependency manager for PHP.

## Usage

- **API Access**: Interact with the API endpoints to manage files and user accounts.
- **Authentication**: Use the provided endpoints for user registration and login.

## API Endpoints

- **User Authentication**
  - `POST /api/register`: Register a new user.
  - `POST /api/login`: Authenticate a user.
- **File Management**
  - `GET /api/files`: List all files.
  - `POST /api/files`: Upload a new file.
  - `GET /api/files/{id}`: Download a file.
  - `DELETE /api/files/{id}`: Delete a file.
