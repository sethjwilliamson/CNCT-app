/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package discrete;

/**
 *
 * @author rachelferrara
 */
public class Discrete {

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        int H[] = {1,0,1,0,0};
        int W[] = {0,0,0,0,0};
        for(int i = 0; i < H.length; i++){
            if(H[i] > W[i]) System.out.println("false");
            System.out.println("true");
        }


    }
       
}
    


