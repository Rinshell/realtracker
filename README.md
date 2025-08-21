# 🚀 CI/CD пайплайн для RealTracker

Мы используем:
- **Docker** — чтобы приложение работало одинаково на любом сервере.  
- **GitHub Actions** — чтобы при каждом пуше в GitHub автоматически собирать Docker-образ.  
- **Docker Hub** — как хранилище готовых образов.  
- **Webhook (опционально)** — для автоматического обновления приложения на сервере.

---

## 1. Docker Hub
Docker Hub будет хранилищем для готового образа приложения.  
Сервер будет скачивать его оттуда.  

1. Зарегистрируйтесь на [Docker Hub](https://hub.docker.com).  
2. Создайте новый репозиторий, например:  
   ```
   your-docker-username/realtracker
   ```  
3. В настройках аккаунта создайте **Access Token** (это безопаснее, чем использовать пароль).  
   - `DOCKERHUB_USERNAME` — ваш логин  
   - `DOCKERHUB_TOKEN` — токен  

---

## 2. GitHub Actions (CI — сборка и публикация образа)
Чтобы **не собирать Docker-образ вручную**.  
GitHub сам будет собирать и публиковать свежий образ при каждом пуше в `main`.  

Создайте файл `.github/workflows/docker-publish.yml`:

```yaml
name: Docker Build and Push

on:
  push:
    branches: [ main ]   # запуск при пуше в main
    tags: [ 'v*' ]       # и при создании тега v1.0, v2.0 и т.п.

env:
  DOCKERHUB_USERNAME: ${{ secrets.DOCKERHUB_USERNAME }}
  DOCKERHUB_TOKEN: ${{ secrets.DOCKERHUB_TOKEN }}
  IMAGE_NAME: your-docker-username/realtracker   # ⚠️ замените username на ваш

jobs:
  build-and-push:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      
      - name: Login to Docker Hub
        uses: docker/login-action@v3
        with:
          username: ${{ secrets.DOCKERHUB_USERNAME }}
          password: ${{ secrets.DOCKERHUB_TOKEN }}
      
      - name: Set up Docker Buildx
        uses: docker/setup-buildx-action@v3
      
      - name: Build and push
        uses: docker/build-push-action@v5
        with:
          context: .
          file: docker-compose/backend/Dockerfile # ⚠️ проверьте путь к Dockerfile
          push: true
          tags: |
            ${{ env.IMAGE_NAME }}:latest
            ${{ env.IMAGE_NAME }}:${{ github.sha }}
          cache-from: type=gha
          cache-to: type=gha,mode=max

      # (Опционально) отправка сигнала на сервер для автодеплоя
      - name: Trigger deployment
        run: |
          curl -X POST http://your-server:9000/hooks/deploy-realtracker   # ⚠️ замените your-server на IP/домен вашего сервера
```

---

## 3. Секреты в GitHub
GitHub Actions не хранит пароли в коде — они задаются как **секреты**.  

В репозитории → **Settings → Secrets and variables → Actions**:  
- `DOCKERHUB_USERNAME` — ваш логин на Docker Hub  
- `DOCKERHUB_TOKEN` — токен доступа  

---

## 4. Настройка сервера (CD — развёртывание)
Сервер будет тянуть свежие образы и запускать их.  

### Установка Docker
```bash
sudo apt update && sudo apt install -y docker.io docker-compose-plugin

# Альтернативный вариант установки Docker и docker-compose-plugin

# Установка Docker
sudo apt update
sudo apt install -y docker.io

# Скачать последнюю версию
sudo curl -L "https://github.com/docker/compose/releases/download/v2.24.5/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose

# Дать права на выполнение
sudo chmod +x /usr/local/bin/docker-compose

# Проверить установку
docker-compose --version
```

### Подготовка проекта
```bash
sudo mkdir -p /opt/realtracker
sudo chown -R $USER:$USER /opt/realtracker
cd /opt/realtracker

# Скопируйте сюда:
# 1. .env 
# 2. docker-compose.yml 

# Пример команды копирования с локальной машины:
scp .env docker-compose.yml user@your-server:/opt/realtracker/
# ⚠️ Замените user на имя пользователя сервера, а your-server на IP или домен
```

На сервере:
```
# Установить git, если нет
sudo apt update && sudo apt install -y git

# Перейти в рабочую папку
sudo mkdir -p /opt/realtracker
sudo chown -R $USER:$USER /opt/realtracker
cd /opt/realtracker

# Клонировать проект
git clone https://github.com/your-username/realtracker.git .
```
---

## 5. Скрипт деплоя
Чтобы одним вызовом обновить приложение.  

Создайте `/usr/local/bin/deploy-realtracker`:

```
sudo nano /usr/local/bin/deploy-realtracker
```

```bash
#!/bin/bash
cd /opt/realtracker
git pull origin main
docker-compose -f docker-compose.prod.yml pull
docker-compose -f docker-compose.prod.yml up -d --force-recreate
docker image prune -f
```

Выдайте права:
```bash
chmod +x /usr/local/bin/deploy-realtracker
```

Теперь достаточно вызвать:
```bash
deploy-realtracker
```
и сервер скачает новый образ и перезапустит проект.  

---

## 6. Webhook (опционально, для авто-деплоя)
Чтобы **сервер обновлялся автоматически** после пуша.  
Без вебхука вы запускаете `deploy-realtracker` вручную.  

### Установка
```bash
sudo apt install webhook
```

### Конфигурация `/etc/webhook.conf`
```json
[
  {
    "id": "deploy-realtracker",
    "execute-command": "/usr/local/bin/deploy-realtracker",
    "command-working-directory": "/opt/realtracker"
  }
]
```

### Запуск
```bash
webhook -hooks /etc/webhook.conf -port 9000 -verbose
```

Теперь GitHub Actions будет слать запрос на сервер (`http://your-server:9000/hooks/deploy-realtracker`), и приложение обновится автоматически.  

👉 Если не хотите автоматизацию — просто пропустите этот шаг.  

---

## 7. Первый запуск
```bash
deploy-realtracker
```

---

## 🔄 Как работает пайплайн
1. Разработчик пушит изменения в `main`  
2. GitHub Actions:
   - собирает Docker-образ  
   - отправляет его в Docker Hub  
   - (опционально) шлёт вебхук на сервер  
3. Сервер:
   - либо обновляется автоматически через вебхук  
   - либо вручную через `deploy-realtracker`  
