FROM node:22-alpine

WORKDIR /var/www/frontend

CMD ["sh", "-c", "if [ -f package.json ]; then npm install && npm run dev -- --host 0.0.0.0 --port 5173; else tail -f /dev/null; fi"]
