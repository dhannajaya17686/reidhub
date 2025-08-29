# 🛠️ Development Issues and Troubleshooting Guide

This guide covers common issues developers may encounter when setting up development environments—especially when using Docker—and how to resolve them effectively.

---

## 🐳 Docker Error: Cannot Start Service `postgres`

### ❌ Error Message

> ❌ERROR: for postgres  Cannot start service postgres: driver failed programming external connectivity on endpoint next_postgres (5f51da281a51dad7c1600d36805e02ae3ecd3e5338ffac89489020c3541ead37): failed to bind port 0.0.0.0:5432/tcp: Error starting userland proxy: listen tcp4 0.0.0.0:5432: bind: address already in use Tip: SQL statements end with a `;`, while `\` commands do not.

### ✅ Solution

#### Option 1: Stop the conflicting process

Check what's using port `5432`:

```bash
lsof -i :5432
```

Then stop the process:

Linux/macOS:

```bash
kill -9 <PID>
```

Windows:

```bash
taskkill /PID <PID> /F
```
---

## 🐳 Docker Error: ERROR: for postgres  'ContainerConfig' KeyError: 'ContainerConfig'

### ❌ Error Message

> ❌ERROR: ERROR: for postgres  'ContainerConfig' KeyError: 'ContainerConfig'

### ✅ Solution

#### Option 1: Stop the docker process and restart again 

Stop and remove all containers, volumes, and networks (if any):
```bash
   docker-compose down
   docker-compose up --build
```
Or if it does not work try this 
```bash
   docker-compose down --volumes --remove-orphans
   docker-compose up --build --recreate
```
**This will essentially delete all the data like volumes and start fresh this is needed if u have created a new schema and stuff**