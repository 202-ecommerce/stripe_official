#+TITLE: moduleTemplate
#+AUTHOR: Prestashop

## Compiling assets
**For development**

We use _Webpack_ to compile our javascript and scss files.  
In order to compile those files, you must :
1. have _Node 10+_ installed locally
2. run `npm install` in the root folder to install dependencies
3. then run `npm run watch` to compile assets and watch for file changes

**For production**

Run `npm run build` to compile for production.  
Files are minified, `console.log` and comments dropped.
