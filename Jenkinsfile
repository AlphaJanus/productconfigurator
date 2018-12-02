node {
    // Clean workspace before doing anything
    deleteDir()
    try {
        stage ('Clone') {
            sh "composer create-project --repository=https://repo.magento.com magento/marketplace-eqp magento-coding-standard"
            sh "mkdir -p module"
            sh "ls -la"
            //checkout scm
        }
        stage ('Build') {
            sh "echo 'shell scripts to build project...'"
            //sh "../magento-coding-standard/vendor/bin/phpcs ./ --standard=MEQP2"
        }
    } catch (err) {
        currentBuild.result = 'FAILED'
        throw err
    }
}
