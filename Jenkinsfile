node {
    // Clean workspace before doing anything
    deleteDir()
    try {
        stage ('Clone') {
            checkout scm
        }
        stage ('Build') {
            sh "echo 'shell scripts to build project...'"
            sh "composer install -vvv"
        }
    } catch (err) {
        currentBuild.result = 'FAILED'
        throw err
    }
}
