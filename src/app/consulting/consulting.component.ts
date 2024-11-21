import { AfterViewInit, Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { MatIconModule } from '@angular/material/icon';
import { YouTubePlayer, YouTubePlayerModule } from '@angular/youtube-player';
import { CommonModule } from '@angular/common';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';

@Component({
  selector: 'app-consulting',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            MatIconModule,
            RouterOutlet, RouterLink, RouterLinkActive,
            YouTubePlayer, YouTubePlayerModule,
            CommonModule,
            ScrollButtonComponent],
  templateUrl: './consulting.component.html',
  styleUrl: './consulting.component.css'
})
export class ConsultingComponent {
  isVideoVisible: boolean[] = [false, false, false]; 
  videos = [ 
    { videoId: 'YX5i8T1ta64', imgsrc: '../../assets/img/consulting/video-1.jpg'}, 
    { videoId: 'Uyfk-4m9Syk', imgsrc: '../../assets/img/consulting/video-2.jpg'}, 
    { videoId: '0Oc1Y_LZXUI', imgsrc: '../../assets/img/consulting/video-3.jpg'} 
  ];
  playerConfig = {
    controls: 0,
    mute: 1,
    autoplay: 1
  };
  toggleVideo(index: number) {
    this.isVideoVisible[index] = !this.isVideoVisible[index]; 
  } 
  hideVideo(index: number) {
      this.isVideoVisible[index] = false; 
    } 
}


